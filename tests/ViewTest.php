<?php

use Mockery as m;
use Robbo\Presenter\View\View;
use Robbo\Presenter\Decorator;

class ViewTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testWithMakesPresentable()
    {
        $factory = new FactoryStub(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            m::mock('Illuminate\Contracts\Events\Dispatcher'),
            new Decorator
        );

        $view = new View($factory, m::mock('Illuminate\View\Engines\EngineInterface'), 'test', 'test/path');

        $view->with('presenter', new ViewPresentableStub);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
    }

    public function testWithMakesArrayPresentable()
    {
        $factory = new FactoryStub(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            m::mock('Illuminate\Contracts\Events\Dispatcher'),
            new Decorator
        );

        $view = new View($factory, m::mock('Illuminate\View\Engines\EngineInterface'), 'test', 'test/path');

        $data = array(
            'presenter' => new ViewPresentableStub
        );

        $view->with($data);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
    }
}
