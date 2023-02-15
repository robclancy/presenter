<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Robbo\Presenter\Decorator;
use Robbo\Presenter\PresentableInterface;
use Robbo\Presenter\Presenter;
use Robbo\Presenter\View\View;

class ViewTest extends TestCase
{
    public function tearDown(): void
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
            'presenter' => new ViewPresentableStub,
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
