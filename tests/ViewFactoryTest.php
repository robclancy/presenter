<?php

use Mockery as m;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\Decorator;
use Robbo\Presenter\View\View;
use Illuminate\Support\Collection;
use Robbo\Presenter\View\Factory;
use Robbo\Presenter\PresentableInterface;

class ViewFactoryTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
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

        $factory = $this->getFactory();
        $factory->finder->shouldReceive('find')->once()->andReturn('test');

        $view = $factory->make('test', $data);

        $this->assertInstanceOf('Robbo\Presenter\View\View', $view);
        $this->assertSame($view['meh'], $data['meh']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presentable']);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presentable']->presentableObject);
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presentable']->getPresentableObject());
        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['collection']['presentable']);
    }

    protected function getFactory()
    {
        return new FactoryStub(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            m::mock('Illuminate\Events\Dispatcher'),
            new Decorator
        );
    }
}

class FactoryStub extends Factory {

    public $finder;

    public function getEngineFromPath($path)
    {
        return m::mock('Illuminate\View\Engines\EngineInterface');
    }
}

class PresentableStub implements PresentableInterface {

    public $presentableObject;

    public function __construct()
    {
        $this->presentableObject = new SecondPresentableStub;
    }

    public function getPresentableObject()
    {
        return $this->presentableObject;
    }

    public function getPresenter()
    {
        return new FactoryPresenterStub($this);
    }
}

class SecondPresentableStub implements PresentableInterface {

    public function getPresenter()
    {
        return new FactoryPresenterStub($this);
    }
}

class FactoryPresenterStub extends Presenter {}