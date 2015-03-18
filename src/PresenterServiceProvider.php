<?php namespace Robbo\Presenter;

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
     *
     * @return void
     */
    public function register()
    {
        $this->registerDecorator();

        $this->registerFactory();
    }

    /**
     * Register the decorator. If you want to extend the decorator you would basically copy
     * what this method does in start/global.php or your own service provider.
     *
     * @return void
     */
    public function registerDecorator()
    {
        $this->app->bindShared('presenter.decorator', function ($app) {
            $decorator = new Decorator;

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
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->bindShared('view', function ($app) {
            $factory = new View\Factory(
                $app->make('view.engine.resolver'),
                $app->make('view.finder'),
                $app->make('events'),
                $app->make('presenter.decorator')
            );

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
        return ['presenter.decorator'];
    }
}
