<?php
namespace Verzth\TCX\Middleware;

use Verzth\TCX\TCX;

class TransactionMiddleware{
    /** @var TCX $tcx */
    protected $tcx;

    public function __construct(TCX $tcx)
    {
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


        return $next($request);
    }
}