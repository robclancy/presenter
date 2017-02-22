<?php

namespace App\Presenters;

use Robbo\Presenter\Presenter;

class UserPresenter extends Presenter
{
    public function presentFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
