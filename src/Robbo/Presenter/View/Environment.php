<?php namespace Robbo\Presenter\View;

use ArrayAccess;
use IteratorAggregate;
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

		$data = array_merge($data, $mergeData);

		return new View($this, $this->getEngineFromPath($path), $view, $path, $this->makePresentable($data));
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
			return parent::share($key, $this->makePresentable($value));
		}

		return parent::share($this->makePresentable($key));
	}

	/**
	 * If this variable implements Robbo\Presenter\PresentableInterface then turn it into a presenter.
	 *
	 * @param  mixed $value
	 * @return mixed $value
	*/
	public function makePresentable($value)
	{
		if ($value instanceof PresentableInterface)
		{
			return $value->getPresenter();
		}

		if (is_array($value) OR ($value instanceof IteratorAggregate AND $value instanceof ArrayAccess))
		{
			foreach ($value AS $k => $v)
			{
				$value[$k] = $this->makePresentable($v);
			}
		}

		return $value;
	}
}
