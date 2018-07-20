<?php
/**
 * Created by PhpStorm.
 * User: Dodi
 * Date: 7/20/2018
 * Time: 10:37 AM
 */

namespace Verzth\TCX;


use Illuminate\Support\ServiceProvider;
use Verzth\TCX\Middleware\TransactionMiddleware;

class TCXServiceProvider extends ServiceProvider{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'cors');

        $this->app->singleton(TCX::class, function ($app) {
            $options = $app['config']->get('tcx');

            if (isset($options['allowedOrigins'])) {
                foreach ($options['allowedOrigins'] as $origin) {
                    if (strpos($origin, '*') !== false) {
                        $options['allowedOriginsPatterns'][] = $this->convertWildcardToPattern($origin);
                    }
                }
            }

            return new TCX($options);
        });
    }

    /**
     * Add the Cors middleware to the router.
     *
     */
    public function boot()
    {
        // Lumen is limited.
        if ($this->isLumen()) {
            $this->app->middleware([TransactionMiddleware::class]);
        } else {
            $this->publishes([$this->configPath() => config_path('tcx.php')]);

            $kernel = $this->app->make(Kernel::class);

            // When the HandleCors middleware is not attached globally, add the PreflightCheck
            if (! $kernel->hasMiddleware(TransactionMiddleware::class)) {
                $kernel->prependMiddleware(TransactionMiddleware::class);
            }
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/tcx.php';
    }

    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen');
    }

    /**
     * Create a pattern for a wildcard, based on Str::is() from Laravel
     *
     * @see https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Str.php
     * @param $pattern
     * @return string
     */
    protected function convertWildcardToPattern($pattern)
    {
        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return '#^'.$pattern.'\z#u';
    }
}