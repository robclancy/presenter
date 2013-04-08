<?php namespace Robbo\Presenter\View;

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
		return parent::with($key, $this->environment->makePresentable($value));
	}
}