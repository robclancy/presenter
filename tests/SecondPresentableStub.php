<?php

use Robbo\Presenter\PresentableInterface;

class SecondPresentableStub implements PresentableInterface
{
    public function getPresenter()
    {
        return new FactoryPresenterStub($this);
    }
}

