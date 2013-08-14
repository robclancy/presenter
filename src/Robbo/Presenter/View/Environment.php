<?php namespace Robbo\Presenter\View;

use Robbo\Presenter\Presenter;
use Robbo\Presenter\Decorator;
use Illuminate\Events\Dispatcher;
use Illuminate\View\ViewFinderInterface;
use Robbo\Presenter\PresentableInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment {

    /**
     * Used for "decorating" objects to have presenters.
     *
     * @var \Robbo\Presenter\Decorator
     */
    protected $presenterDecorator;

    /**
     * Create a new view environment instance.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $engines
     * @param  \Illuminate\View\ViewFinderInterface  $finder
     * @param  \Illuminate\Events\Dispatcher  $events
     * @param  \Robbo\Presenter\Decorator  $decorator
     * @return void
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events, Decorator $decorator)
    {
        $this->presenterDecorator = $decorator;

        parent::__construct($engines, $finder, $events);
    }

    /**
     * Get a evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return Illuminate\View\View
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        $path = $this->finder->find($view);

        $data = array_merge($mergeData, $this->parseData($data));

        return new View($this, $this->getEngineFromPath($path), $view, $path, $this->decorate($data));
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function share($key, $value = null)
    {
        if ( ! is_array($key))
        {
            return parent::share($key, $this->decorate($value));
        }

        return parent::share($this->decorate($key));
    }

    /**
     * Decorate an object with a presenter.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function decorate($value)
    {
        return $this->presenterDecorator->decorate($value);
    }

}
