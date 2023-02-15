<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Robbo\Presenter\PresentableInterface;

class User extends Authenticatable implements PresentableInterface
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Return a created presenter.
     *
     * @return Robbo\Presenter\Presenter
     */
    public function getPresenter()
    {
        return new Presenters\UserPresenter($this);
    }
}
