<?php

use Robbo\Presenter\Presenter;

class PresenterStub extends Presenter
{
    public $testVar2 = 'testvar2';

    public function testMethod2()
    {
        return 'testMethod2';
    }

    protected function presentAwesome()
    {
        return 'presenting you the awesome';
    }
}
