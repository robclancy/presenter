<?php

use Mockery as m;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\PresentableInterface;
use Robbo\Presenter\View\Environment;
use Illuminate\Support\Collection;

class ViewEnvironmentTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->env = new EnvironmentStub;
	}

	public function tearDown()
	{
		m::close();
	}

	public function testPresentableToPresenter()
	{
		$data = $this->env->testMakePresentable(array(
			'string' => 'string',
			'array' => array('test' => 'test'),
			'presentable' => new PresentableStub
		));

		$this->assertTrue($data['presentable'] instanceof EnvPresenterStub);
	}	

	public function testPresentablesInCollectionToPresenters()
	{
		$data = $this->env->testMakePresentable(array(
			'string' => 'string',
			'array' => array('test' => 'test'),
			'presentable' => new PresentableStub,
			'collection' => new Collection(array(
				'presentable' => new PresentableStub
			))
		));

		$this->assertTrue($data['presentable'] instanceof EnvPresenterStub);
		$this->assertTrue($data['collection']['presentable'] instanceof EnvPresenterStub);
	}
}

class EnvironmentStub extends Environment {

	public function __construct()
	{
		parent::__construct(
			m::mock('Illuminate\View\Engines\EngineResolver'), 
			m::mock('Illuminate\View\FileViewFinder'),
			m::mock('Illuminate\Events\Dispatcher')
		);
	}

	public function testMakePresentable(array $data)
	{
		return $this->makePresentable($data);
	}
}

class PresentableStub implements PresentableInterface {

	public function getPresenter()
	{
		return new EnvPresenterStub($this);
	}
}

class EnvPresenterStub extends Presenter {
}