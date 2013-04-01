<?php namespace Robbo\Presenter\View;

use IteratorAggregate;
use Illuminate\View\View;
use Robbo\Presenter\PresentableInterface;
use Illuminate\View\Environment as BaseEnvironment;

class Environment extends BaseEnvironment {

	/**
	 * Get a evaluated view contents for the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return Illuminate\View\View
	 */
	public function make($view, array $data = array())
	{
		return parent::make($view, $this->makePresentable($data));
	}

	/**
	 * Turn any PresenatableInterface'd objects into Presenters
	 *
	 * @param  array $data
	 * @return array $data
	 */
	protected function makePresentable($data)
	{
		foreach ($data AS $key => $value)
		{
			if ($value instanceof PresentableInterface)
			{
				$data[$key] = $value->getPresenter();
			}
			else if (is_array($value) OR $value instanceof IteratorAggregate)
			{
				$data[$key] = $this->makePresentable($value);
			}
		}

		return $data;
	}
}