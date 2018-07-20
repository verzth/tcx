<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 01/25/2017
 * Time: 4:11 PM
 */

namespace Silverius\OWTC\Middleware;

use Illuminate\Http\Request;
use Silverius\OWTC\Models\Application;
use Silverius\OWTC\Models\Group;
use Silverius\OWTC\Traits\GeneralResponse;
use Closure;

class OWTCAuthentication{
    use GeneralResponse;
    protected $except = [];

    public function handle($request, Closure $next){
        if(!$this->shouldPassThrough($request)) {
            if ($request->hasHeader('x-owtc-type')
                && $request->hasHeader('x-owtc-app-id') && $request->hasHeader('x-owtc-app-public')
                && $request->hasHeader('x-owtc-id') && $request->hasHeader('x-owtc-key')
                && $request->hasHeader('x-owtc-token') && strlen($request->header('x-owtc-id')) == 64 && strlen($request->header('x-owtc-key')) > 0) {
                if (strtoupper($request->header('x-owtc-type')) == 'GROUP') {
                    $auth = Group::where('identity', $request->header('x-owtc-app-id'))
                        ->active()->first();
                } elseif (strtoupper($request->header('x-owtc-type')) == 'APPLICATION') {
                    $auth = Application::where('identity', $request->header('x-owtc-app-id'))
                        ->active()->first();
                } else {
                    $this->response->status_code = 'ATNS';
                    $this->response->status_number = 0x02;
                    $this->response->status_message = 'Authentication Type not supported';
                    $this->debug($request->header('app_id'));
                    return response()->jsonp($request->get('callback'), $this->response);
                }
                if ($auth) {
                    if ($request->header('x-owtc-app-public') == serverKeyGeneration($auth->identity, $auth->public)) {
                        if ($request->header('x-owtc-key') == clientKeyGeneration($auth->secret, $request->header('x-owtc-token'), $request->header('x-owtc-id'))) {
                            if ($auth instanceof Application) {
                                $request->attributes->add(['_application_id_' => $auth->id]);
                                $request->attributes->add(['_group_id_' => $auth->group_id]);
                            } elseif ($auth instanceof Group) {
                                $request->attributes->add(['_group_id_' => $auth->id]);
                            }
                            return $next($request);
                        } else {
                            $this->response->status_code = 'AOAF';
                            $this->response->status_number = 0x05;
                            $this->response->status_message = 'OWTC Authentication Failed';
                            $this->debug(clientKeyGeneration($auth->secret, $request->header('x-owtc-token'), $request->header('x-owtc-id')));
                            return response()->jsonp($request->get('callback'), $this->response);
                        }
                    } else {
                        $this->response->status_code = 'AAFX';
                        $this->response->status_number = 0x04;
                        $this->response->status_message = 'Authentication Failed';
                        $this->debug(serverKeyGeneration($auth->identity, $auth->public));
                        return response()->jsonp($request->get('callback'), $this->response);
                    }
                } else {
                    $this->response->status_code = 'ANFX';
                    $this->response->status_number = 0x03;
                    $this->response->status_message = 'ID Not Found';
                    $this->debug($request->header('x-owtc-app-id'));
                    return response()->jsonp($request->get('callback'), $this->response);
                }
            } else {
                $this->response->status_code = 'AARQ';
                $this->response->status_number = 0x01;
                $this->response->status_message = 'Authentication Required';
                $this->debug($request->header('x-owtc-type'));
                return response()->jsonp($request->get('callback'), $this->response);
            }
        }else{
            return $next($request);
        }
    }

    protected function shouldPassThrough(Request$request){
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}