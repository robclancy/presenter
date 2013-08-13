# Presenter

This library adds a simple class to help make a `Presenter` for your objects or arrays. It also has little extras for use within Laravel with minimal extra code in your controllers (in most cases no extra code).

[![Build Status](https://secure.travis-ci.org/robclancy/presenter.png)](http://travis-ci.org/robclancy/presenter)

## Table of Contents

- <a href="#installation">Installation</a>
    - <a href="#composer">Composer</a>
    - <a href="#manually">Manually</a>
    - <a href="#laravel-4">Laravel 4</a>
- <a href="#usage">Usage</a>
	- <a href="#general-usage">General Usage</a>
	- <a href="#manually-initiate">Manually Initiate</a>
	- <a href="#laravel-usage">Laravel Usage</a>
	- <a href="#array-usage">Array Usage</a>
	- <a href="#extending-the-decorator">Extending the Decorator</a>
- <a href="#change-log">Change Log</a>
- <a href="#license">License</a>

## Installation

### Composer

Add `robclancy/presenter` to the "require" section of your `composer.json` file.

```json
	"robclancy/presenter": "1.1.*"
```

Run `composer update` to get the latest version of the package.


### Manually

It's recommended that you use Composer, however you can download and install from this repository.


### Laravel 4

This package comes with an optional service provider for Laravel 4 so that you can automate some extra steps. You will need to have installed using the composer method above, then register the service provider with your application.

Open `app/config/app.php` and find the `providers` key. Add 
```
'Robbo\Presenter\PresenterServiceProvider',
``` 
to the array at some point after 
```
'Illuminate\View\ViewServiceProvider',
```

Now presenters will automatically be created if using the <a href="#laravel-usage">laravel method</a> described below.


## Usage

`Presenter` is a very simply class that overloads methods and variables so that you can add extra logic to your objects or arrays without adding view logic to areas like your models or controllers and also keeps any extra logic our of your views.

### General Usage

Let's say you have a list of users and you want to generate a link to the profile of each user. Many people would just build the URL in the view, or worse, in the controller. To separate this logic we instead use a presenter. Let's assume we have a `User` class which simply has an `id` and `username` property. The presenter might look like this.

```php

class UserPresenter extends Robbo\Presenter\Presenter {
	
	public function url()
	{
		return $this->id.'-'.$this->username;
	}
}
```

Now our view should receive an instance of this presenter which would be created with something like `$user = new PresenterUser(new User);`. If we want to link to the users page all we have to do is call `$user->url()`. Now you have good separation of logic and an easy little class you can modify to add properties to your `User` in all areas.
However you might not want to be calling methods like this, it could be inconsistant with what you are doing or you might want the code to look a little cleaner. That is where methods with the `present` prefix come in. All we do is update the presenter to the following.

```php

class UserPresenter extends Robbo\Presenter\Presenter {
	
	public function presentUrl()
	{
		return $this->id.'-'.$this->username;
	}
}
```

Now the presenter will call this new method when you execute `$user->url`. Further more you can access this method via `ArrayAccess` by calling `$user['url']`. More on `ArrayAccess` support below.


### Manually Initiate

As mentioned in the above section to create a presenter you simply initiate with the `new` keyword and inject your object or array.

```php

class User {
	// ...
}

class UserPresenter extends Robbo\Presenter\Presenter {
	
	// ...
}

$user = new User;

// handle stuff here

// make sure to "convert" to a presenter before the object gets to your views
$user = new UserPresenter($user);


// Can also create a presenter for arrays
$user = [
	'id' => 1,
	'username' => 'Robbo',
];

// same as before
$user = new UserPresenter($user);
```


### Laravel Usage

If you are using laravel and have followed the above installation instructions you can use the provided interface `Robbo\Presenter\PresentableInterface` to automate turning your model instances into a `Presenter` from both collections and when a model is sent directly to the view.

What the service provider does is extend Laravel's view component with a step before the view object is created. This step turns anything that implements the `PresentableInterface` into a `Presenter` by calling `->getPresenter()`. What this means is you don't need to add anything extra to your controllers to have your views using presenters for your objects.

For Example.

```php

class UserPresenter extends Robbo\Presenter\Presenter {
	
	// ...
}

class User implements Robbo\Presenter\PresentableInterface {
	
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

Now whenever your `User` model is sent to a view, in a collection, array or by itself it will be turned into a presenter using the provided `getPresenter()` method. So your controller will work with `User` and when you get to your view it will be working with `UserPresenter` with the internal object being `User`.


### Array Usage

1.1.x introduces support for arrays. The `Presenter` will implement `ArrayAccess` so in your views you can access your variables with `$presenter['variable']` if you want. But more importantly you can give the `Presenter` an array instead of an object. So you can use presenters to work with array data as well as objects.

For example.

```php

$user = [
	'id' => 1,
	'username' => 'Robbo',
];

class UserPresenter extends Robbo\Presenter\Presenter {
	
	public function presentUrl()
	{
		// This will work exactly the same as previous examples
		return $this->id.'-'.$this->username;

		// You can also do this...
		return $this['id'].'-'.$this['username'];
	}
}

// Now we create a presenter much the same as before
$user = new UserPresenter($user);


// In our views we can use the $user as if it were still an array
echo 'Hello, ', $user['username'];

// Or even treat it like the object that it is
echo 'Hello, ', $user->username;

// And like other examples, we can present the url in the same way
echo 'The URL: ', $user->url;
echo 'And again: ', $user['url'];

```

### Extending the Decorator

As of 1.2.x I have added in a decorator object. This object takes care of turning an object that has `PresentableInterface` into a `Presenter`.
This is by default done with Laravel's `View` objects. The reasoning behind a new class instead of the old way is so it can be better tested and also to allow you to extend it.
Here is an example of extending the `Decorator` so that instead of using the `PresentableInterface` and `getPresenter()` method you can use a public variable on the object called `$presenter`.

Note: these instructions are for Laravel usage.

First extend the decorator...

```php

use Robbo\Presenter\Decorator as BaseDecorator;

class Decorator extends BaseDecorator {
	
	/*
     * If this variable implements Robbo\Presenter\PresentableInterface then turn it into a presenter.
     *
     * @param  mixed $value
     * @return mixed $value
    */
    public function decorate($value)
    {
    	if (is_object($value) and isset($value->presenter))
    	{
    		$presenter = $value->presenter;
    		return new $presenter;
    	}

    	return parent::decorate($value);
    }
}
```

And then to use your new decorator either add the following to `start/global.php` or into your own service provider.

```php

// In start/global.php

App::make('presenter.decorator', App::share(function($app)
{
	$decorator = new Decorator;

	Robbo\Presenter\Presenter::setExtendedDecorator($decorator);
	return $decorator;
});

// In a service provider's 'register' method

$this->app['presenter.decorator'] = $this->app->share(function($app)
{
	$decorator = new Decorator;

	Robbo\Presenter\Presenter::setExtendedDecorator($decorator);
	return $decorator;
});


```

And that is all there is to it. You can easily change things to be more automated for creating presenters using this method.


## Change Log

### 1.2.0
- presenters can now be nested, thanks [https://github.com/robclancy/presenter/pull/10](alexwhitman)
- added support for using Laravel's `View::with(array here)`, thanks [https://github.com/robclancy/presenter/pull/14](skovachev)
- added ability to use `isset(...)` and `unset(...)` on presenter variables, thanks [https://github.com/robclancy/presenter/pull/15](nsbucky)
- added a new decorator for creating the presenter objects. This makes it so you can <a href="#extending-the-decorator">extend what happens when decorating an object</a> easily

### 1.1.0
- the Presenter class now implements ArrayAccess
- added ability to use an array as your internal data


### 1.0.2

- fixed bug caused by laravel update
- added smarter converting of presenters from PresentableInterface'd objects
- added object getter `getObject` to retrieve the internal object


### 1.0.1

- fixed bug caused by laravel update

### 1.0.0

- Initial Release


### License

Presenter is released under the [http://www.dbad-license.org/](DBAD) license. Do what you want just **d**on't **b**e **a** **d**ick.
