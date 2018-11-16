<?php

namespace Modules\Icommercepayu\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Icommercepayu\Events\Handlers\RegisterIcommercePayuSidebar;

class IcommercePayuServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
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
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIcommercePayuSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('payuconfigs', array_dot(trans('icommercepayu::payuconfigs')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('Icommercepayu', 'permissions');
        $this->publishConfig('Icommercepayu', 'settings');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Icommercepayu\Repositories\PayuconfigRepository',
            function () {
                $repository = new \Modules\Icommercepayu\Repositories\Eloquent\EloquentPayuconfigRepository(new \Modules\Icommercepayu\Entities\Payuconfig());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercepayu\Repositories\Cache\CachePayuconfigDecorator($repository);
            }
        );
// add bindings

    }
}
