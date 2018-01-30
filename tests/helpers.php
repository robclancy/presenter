<?php

function test_engine_interface()
{
    if (interface_exists('Illuminate\View\Engines\EngineInterface')) {
        return 'Illuminate\View\Engines\EngineInterface';
    } 

    return 'Illuminate\Contracts\View\Engine';
}
