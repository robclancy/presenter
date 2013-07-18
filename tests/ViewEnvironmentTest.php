<?php

use Mockery as m;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\View\View;
use Illuminate\Support\Collection;
use Robbo\Presenter\View\Environment;
use Robbo\Presenter\PresentableInterface;

class ViewEnvironmentTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testPresentableToPresenter()
	{
		$presenter = Presenter::makePresentable(new PresentableStub);

		$this->assertInstanceOf('Robbo\Presenter\Presenter', $presenter);
	}

	public function testPresentablesToPresenters()
	{
		$from = array(
			'string' => 'string',
			'array' => array('test' => 'test'),
			'presentable' => new PresentableStub,
			'recurseMe' => array(array('presentable' => new PresentableStub)),
			'collection' => new Collection(array(
				'presentable' => new PresentableStub
			))
		);

		$to = Presenter::makePresentable($from);

		$this->assertSame($from['string'], $to['string']);
		$this->assertSame($from['array'], $to['array']);
		$this->assertInstanceOf('Robbo\Presenter\Presenter', $to['presentable']);
		$this->assertInstanceOf('Robbo\Presenter\Presenter', $to['recurseMe'][0]['presentable']);
		$this->assertInstanceOf('Robbo\Presenter\Presenter', $to['collection']['presentable']);
	}

	public function testMakeView()
	{
		$data = array(
			'meh' => 'zomg',
			'presentable' => new PresentableStub,
			'collection' => new Collection(array(
				'presentable' => new PresentableStub
			))
		);

		$env = $this->getEnvironment();
		$env->finder->shouldReceive('find')->once()->andReturn('test');

		$view = $env->make('test', $data);

		$this->assertInstanceOf('Robbo\Presenter\View\View', $view);
		$this->assertSame($view['meh'], $data['meh']);
		$this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presentable']);
		$this->assertInstanceOf('Robbo\Presenter\Presenter', $view['collection']['presentable']);
	}

	protected function getEnvironment()
	{
		return new EnvironmentStub(
			m::mock('Illuminate\View\Engines\EngineResolver'),
			m::mock('Illuminate\View\ViewFinderInterface'),
			m::mock('Illuminate\Events\Dispatcher')
		);
	}
}

class EnvironmentStub extends Environment {

	public $finder;

	protected function getEngineFromPath($path)
	{
		return m::mock('Illuminate\View\Engines\EngineInterface');
	}
}

class PresentableStub implements PresentableInterface {

	public function getPresenter()
	{
		return new EnvPresenterStub($this);
	}
}

class EnvPresenterStub extends Presenter {}