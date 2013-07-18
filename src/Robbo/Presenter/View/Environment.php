<?php namespace Robbo\Presenter\View;

use Robbo\Presenter\Presenter;
use Robbo\Presenter\PresentableInterface;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment {

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

		return new View($this, $this->getEngineFromPath($path), $view, $path, Presenter::makePresentable($data));
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
			return parent::share($key, Presenter::makePresentable($value));
		}

		return parent::share(Presenter::makePresentable($key));
	}

}
