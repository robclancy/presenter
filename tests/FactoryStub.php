<?php

use Mockery as m;
use Illuminate\View\View;
use Robbo\Presenter\View\Factory;

class FactoryStub extends Factory
{
    public $finder;

    public function getEngineFromPath($path)
    {
        return m::mock('Illuminate\View\Engines\EngineInterface');
    }

    public function callCreator(View $view)
    {
        return $view;
    }
}
