<?php
namespace Verzth\TCX\Middleware;

use Illuminate\Http\Request;
use Verzth\TCX\TCX;

class TransactionMiddleware{
    /** @var TCX $tcx */
    protected $tcx;
    protected $except = [];

    public function __construct(TCX $tcx){
        $this->tcx = $tcx;
    }

    /**
     * Handle an incoming Preflight request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if($this->shouldPassThrough($request)){

            return $next($request);
        }else{
            return $next($request);
        }
    }

    protected function shouldPassThrough(Request $request){
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