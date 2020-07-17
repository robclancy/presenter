<?php

namespace Robbo\Presenter\View;

use Robbo\Presenter\Decorator;
use Robbo\Presenter\Presenter;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as BaseFactory;

class Factory extends BaseFactory
{
    /**
     * Used for "decorating" objects to have presenters.
     *
     * @var \Robbo\Presenter\Decorator
     */
    protected $presenterDecorator;

    /**
     * Create a new view factory instance.
     *
     * @param \Illuminate\View\Engines\EngineResolver $engines
     * @param \Illuminate\View\ViewFinderInterface $finder
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @param \Robbo\Presenter\Decorator $decorator
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events, Decorator $decorator)
    {
        $this->presenterDecorator = $decorator;

        parent::__construct($engines, $finder, $events);
    }

    /**
     * Create a new view instance from the given arguments.
     *
     * @param  string  $view
     * @param  string  $path
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function viewInstance($view, $path, $data)
    {
        return new View($this, $this->getEngineFromPath($path), $view, $path, $this->decorate($data));
    }

    /**
     * Add a piece of shared data to the factory.
     *
     * @param string $key
     * @param mixed $value
     */
    public function share($key, $value = null)
    {
        if (! is_array($key)) {
            return parent::share($key, $this->decorate($value));
        }

        return parent::share($this->decorate($key));
    }

    /**
     * Decorate an object with a presenter.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function decorate($value)
    {
        return $this->presenterDecorator->decorate($value);
    }
}
