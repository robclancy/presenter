<?php

namespace Robbo\Presenter;

use Illuminate\Support\ServiceProvider;

class PresenterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerDecorator();

        $this->registerFactory();
    }

    /**
     * Register the decorator. If you want to extend the decorator you would basically copy
     * what this method does in start/global.php or your own service provider.
     */
    public function registerDecorator()
    {
        $this->app->singleton('presenter.decorator', function ($app) {
            $decorator = new Decorator();

            // This isn't really doing anything here however if you want to extend the decorator
            // with your own instance then you need to do it like this in your own service
            // provider or in start/global.php.
            Presenter::setExtendedDecorator($decorator);

            return $decorator;
        });
    }

    /**
     * Copied from the view service provider...
     *
     * Register the view factory.
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // factory. The resolver will be used by a factory to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $factory = new View\Factory($resolver, $finder, $app['events'], $app['presenter.decorator']);

            // We will also set the container instance on this view factory since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);

            $factory->share('app', $app);

            return $factory;
        });
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
}
