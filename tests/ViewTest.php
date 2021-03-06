<?php

use Mockery as m;
use Robbo\Presenter\View\View;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\Decorator;
use PHPUnit\Framework\TestCase;
use Robbo\Presenter\PresentableInterface;

class ViewTest extends TestCase
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
            m::mock('Illuminate\Events\Dispatcher'),
            new Decorator
        );

        $view = new View($factory, m::mock(test_engine_interface()), 'test', 'test/path');

        $view->with('presenter', new ViewPresentableStub);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
    }

    public function testWithMakesArrayPresentable()
    {
        $factory = new FactoryStub(
            m::mock('Illuminate\View\Engines\EngineResolver'),
            m::mock('Illuminate\View\ViewFinderInterface'),
            m::mock('Illuminate\Events\Dispatcher'),
            new Decorator
        );

        $view = new View($factory, m::mock(test_engine_interface()), 'test', 'test/path');

        $data = [
            'presenter' => new ViewPresentableStub
        ];

        $view->with($data);

        $this->assertInstanceOf('Robbo\Presenter\Presenter', $view['presenter']);
    }
}

class ViewPresentableStub implements PresentableInterface
{
    public function getPresenter()
    {
        return new ViewPresenterStub($this);
    }
}

class ViewPresenterStub extends Presenter
{
}
