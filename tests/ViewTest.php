<?php

use Mockery as m;
use Robbo\Presenter\View\View;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\Decorator;
use Robbo\Presenter\PresentableInterface;

class ViewTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testWithMakesPresentable()
	{
		$env = new EnvironmentStub(
			m::mock('Illuminate\View\Engines\EngineResolver'),
			m::mock('Illuminate\View\ViewFinderInterface'),
			m::mock('Illuminate\Events\Dispatcher'),
			new Decorator
		);

		$view = new View($env, m::mock('Illuminate\View\Engines\EngineInterface'), 'test', 'test/path');

		$view->with('presenter', new ViewPresentableStub);

		$this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
	}

	public function testWithMakesArrayPresentable()
	{
		$env = new EnvironmentStub(
			m::mock('Illuminate\View\Engines\EngineResolver'),
			m::mock('Illuminate\View\ViewFinderInterface'),
			m::mock('Illuminate\Events\Dispatcher'),
			new Decorator
		);

		$view = new View($env, m::mock('Illuminate\View\Engines\EngineInterface'), 'test', 'test/path');

		$data = array(
			'presenter' => new ViewPresentableStub
		);

		$view->with($data);

		$this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
	}
}

class ViewPresentableStub implements PresentableInterface {

	public function getPresenter()
	{
		return new ViewPresenterStub($this);
	}
}

class ViewPresenterStub extends Presenter {}