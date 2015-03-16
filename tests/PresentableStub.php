<?php

use Robbo\Presenter\PresentableInterface;

class PresentableStub implements PresentableInterface
{
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
