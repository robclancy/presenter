<?php

use Robbo\Presenter\PresentableInterface;

class ViewPresentableStub implements PresentableInterface
{
    public function getPresenter()
    {
        return new ViewPresenterStub($this);
    }
}
