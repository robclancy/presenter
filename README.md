# Presenter

This is a simple class to help make a `Presenter` for your objects. It also has little extras to work with Laravel to automate the creation of the Presenter if the `PresentableInterface` is implemented when passed into the `View`.

[![Build Status](https://secure.travis-ci.org/robclancy/presenter.png)](http://travis-ci.org/robclancy/presenter)

## Installation

Add the following to the "require" section of your `composer.json` file:

```json
	"robclancy/presenter": "1.0.x"
```

#### Extra Laravel Step

Add the following to your `app/config/app.php`, `providers` array (has to be after the `ViewServiceProvider`):

```php
	'Robbo\Presenter\PresenterServiceProvider',
```

## Basic Usage

The idea is to 'wrap' your object (generally a model) with a presenter to contain your view logic. This can either be done by simply creating methods in the presenter or you can make `present` methods for variables. For example calling `$presenter->name` will call `$presenter->presentName` if the method exists.

#### Examples

First our basic data model.
```php
class UserModel {
	public $uniqueId = 1;

	public $firstName = 'Bazza';

	public $lastName = 'Pitt';

	public $nickName = null;

	public $email = 'email@bourbon.com';
}
```

Now we don't want to bog down our views with logic for things like showing the full name if the nickname isn't set. So we use a presenter.
```php
use Robbo\Presenter\Presenter;

class UserPresenter extends Presenter {
	
	// This will be called when the 'name' variable is called on the object
	public function presentName()
	{
		// nickName here will fall through to the model, the same as calling $this->object->nickName, object being the model
		if ( ! is_null($this->nickName))
		{
			return $this->nickName;
		}

		return $this->firstName.' '.$this->lastName;
	}
}
```

Now instead of passing through an object of `UserModel` to your view you pass through `UserPresenter`. For example...
```php
class UserController {
	
	public function index()
	{
		$user = new UserModel;

		// Before going through to the view we wrap this in the presenter
		$user = new UserPresenter($user);

		// .. pass through to view here ..
	}
}
```
Now instead of your view containing logic to check for a nick name or even your model doing the same you can just call name directly... `$user->name` will fall through to `$user->presentName()`.

Also any other variables called that don't exist in the `Presenter` or don't have a `present{Variable}` method will fall through to the original object. So in our case here `$user->firstName` is going to return `Bazza`.

Lastly as you would expect, creating new methods here can be a nice feature to take logic out of your views as well. For example you could make a `url($action)` method to link to things like edit and delete on the user. For example...

```php

use Robbo\Presenter\Presenter;

class UserPresenter extends Presenter {
	
	public function url($action = '')
	{
		return 'users/'.$this->uniqueId.'/'.$action;
	}
}
```

Then link to various areas by going `$user->url('edit')` etc. However we would probably use `presentEditUrl` here instead, like so...
```php

use Robbo\Presenter\Presenter;

class UserPresenter extends Presenter {
	
	public function url($action = '')
	{
		return 'users/'.$this->uniqueId.'/'.$action;
	}

	public function presentEditUrl()
	{
		return $this->url('edit');
	}
}
```

Now we can get the edit URL with `$user->editUrl;`. 

That pretty much covers the use of this class, just simple little helpers.


## Laravel Usage

Everything works the same as above however if you use the `PresenterServiceProvider` explained in installation you can instead implement `Robbo\Presenter\PresentableInterface` on your models and creating the interface will happen automatically by your views everywhere.

So the same `UserModel` example above would be modified to be...
```php
use Robbo\Presenter\PresentableInterface;

class UserModel implements PresentableInterface {
	public $uniqueId = 1;

	public $firstName = 'Bazza';

	public $lastName = 'Pitt';

	public $nickName = null;

	public $email = 'email@bourbon.com';

	/**
	 * Return a created presenter.
	 *
	 * @return Robbo\Presenter\Presenter
	 */
	public function getPresenter()
	{
		return new UserPresenter($this);
	}
}
```

Now instead of making the presenter in the controller the Laravel `View` objects will do it for you so your controllers don't change at all but you get the presenter in your view.

So here is our Laravel Controller.
```php

class UserController extends Controller {
	
	public function getIndex()
	{
		return View::make('user.index', [
			'user' => new UserModel
		]);
	}
}
```

Behind the scenes the `View` will detect the implemented `PresentableInterface` and convert the view into the `Presenter` by calling `getPresenter()`. This automatic presenter creation will also work with `View::share` and `View->with()`. It will also recursively make presenters in arrays or objects that are acting like arrays (Illuminate\Support\Collection for example).
