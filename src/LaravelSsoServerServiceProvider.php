<?php

namespace AdityaDarma\LaravelSsoServer;

use AdityaDarma\LaravelJwtSso\Facades\SsoCrypt;
use AdityaDarma\LaravelJwtSso\Facades\SsoJwt;
use AdityaDarma\LaravelSsoServer\Console\SsoServerInstall;
use Illuminate\Support\ServiceProvider;

class LaravelSsoServerServiceProvider extends ServiceProvider
{
    public const CONFIG_PATH = __DIR__ . '/../config/sso-server.php';


    /**
     * Publish data.
     *
     * @return void
     */
    private function publish(): void
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('sso-server.php')
        ], 'config');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publish();

        SsoJwt::setSecretKey(config('sso-server.secret_key'));
        SsoCrypt::setSecretKey(config('sso-server.secret_key'));
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'sso-server'
        );

        $this->app->bind('sso-server', function() {
            return new SsoServer();
        });

        $this->commands([SsoServerInstall::class]);
    }
}
