<?php namespace Antonyflame\Ioiopay\Laravel;

use Illuminate\Support\ServiceProvider;

class IoiopayServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ioiopay', function() {
            return $this->getIoiopayService();
        });
    }

    public function boot() {
         $this->publishes([
            __DIR__.'/../../config/ioiopay.php' => config_path('ioiopay.php'),
         ], 'config');
    }

    protected function getIoiopayService() {
        $appID = config('ioiopay.app_id');
        $ak = config('ioiopay.ak');
        $sk = config('ioiopay.sk');
        $publicKey = "-----BEGIN PUBLIC KEY-----\n"
            . config('ioiopay.public_key')
            . "\n-----END PUBLIC KEY-----";

        return new IoiopayService($appID, $ak, $sk, $publicKey);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ioiopay'];
    }
}
