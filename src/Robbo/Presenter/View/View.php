<?php namespace Robbo\Presenter\View;

use Robbo\Presenter\Presenter;
use Illuminate\View\View as BaseView;

class View extends BaseView {

	/**
	 * Add a piece of data to the view.
	 *
	 * @param  string|array  $key
	 * @param  mixed   $value
	 * @return \Illuminate\View\View
	 */
	public function with($key, $value = null)
	{
		return parent::with($key, Presenter::makePresentable($value));
	}
}