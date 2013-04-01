## Presenter

Simple presenter to wrap and render objects. Designed to work with Laravel, but will also work as a stand-alone solution.

[![Build Status](https://secure.travis-ci.org/robclancy/presenter.png)](http://travis-ci.org/robclancy/presenter)

### Installation

Add the following to the "require" section of your `composer.json` file:

```json
	"robclancy/presenter": "dev-master"
```

### Basic Usage
The core idea is the relationship between two classes: your model full of data and a presenter which works as a sort of wrapper to help with your views.
For instance, if you have a `User` object you might have a `UserPresenter` presenter to go with it. To use it all you do is `$userObject = new UserPresenter($userObject);`. 
The `$userObject` will function the same unless a method is called that is a member of the `UserPresenter`. Another way to think of it is that any call that doesn't exist in the `UserPresenter` falls through to the original object. There are some full examples below.

### Usage Within Laravel
Laravel has several shortcuts that you can use, but first you must add the Service Provider. Add the following to your `app/config/app.php`, `providers` array (has to be after the `ViewServiceProvider`):

```php
	'Robbo\Presenter\PresenterServiceProvider',
```

Now you can implement the interface `Robbo\Presenter\PresentableInterface` on your models and when you do, the `View` will automatically turn your model into the defined presenter. So you will just pass your normal object to `View::make(...)` and it will handle the rest for use within your views. Also examples below.

You might also like to alias the classes so you don't have to write out the namespaces over and over. To do this add the following to your `app/config/app.php`, `aliases` array:
```php
	'Presenter' 	=> 'Robbo\Presenter\Presenter',
	'Presentable'	=> 'Robbo\Presenter\PresentableInterface',
```

### Examples
Note: these examples use a made up `slugify` method

**Example Model**
```php
// Assume this data is loaded into the model
class User {

	public $uniqueId = 1;

	public $firstName = 'Bazza';

	public $lastName = 'Pitt';

	public $email = 'email@bourbon.com';
}
```
	
**Example Presenter**
```php
class UserPresenter extends Robbo\Presenter\Presenter {
	
	public function getUrl()
	{
		return 'members/'.slugify($this->firstName);
	}
}
```

**Example Controller**
```php
class UserController {

	public function index()
	{
		$user = new User;

		return $this->someViewMethod('view_name', ['user' => new UserPresenter($user)]);
	}
}
```

**Example View**
```php
<h2><? echo $user->firstName; ?></h2>

<a href="<? echo $user->getUrl(); ?>">Link To This User</a>
```

### Advanced Example
**Another Example Presenter**
```php
class AdminUserPresenter extends Robbo\Presenter\Presenter {

	public function getEditUrl()
	{
		return 'members/'.$this->uniqueId.'/edit';
	}
}
```

**Example Controller Modified**
```php
class UserController {

	public function index()
	{
		$user = new User;

		$user = new UserPresenter($user);
		$user = new AdminUserPresenter($user);

		// $user will now contain all new methods/variables from both presenters

		return $this->someViewMethod('view_name', ['user' => $user]);
	}
}
```

### Laravel Example
**Example Model**
```php
use Robbo\Presenter\PresentableInterface;

class Topic extends Eloquent implements PresentableInterface
{
	public static function recent($count = 5)
	{
		return static::with('author')->orderBy('created_at', 'desc')->take($count)->get();
	}

	public function author()
	{
		return $this->belongsTo('User', 'user_id');
	}

	public function tags()
	{
		return $this->hasMany('Tag');
	}

	public function getPresenter()
	{
		return new TopicPresenter($this);
	}
}
```

**Example Presenter**
```php
use Robbo\Presenter\Presenter;

class TopicPresenter extends Presenter
{
	public function url()
	{
		return URL::action('TopicController@show', [slugify($this->title).'.'.$this->id]);
	}

	public function publishedDate()
	{
		return date('d-m-y', strtotime($this->created_at));
	}
}
```

**Example Controller**
```php
class TopicController extends Controller {
	public function getIndex()
	{
		$recentTopics = Topic::recent(100);

		return View::make('topics/index', ['recentTopics' => $recentTopics]);
	}
}
```

Note: between the above controller and getting the the view the `Topic` objects in the `$recentTopics` collection will be turned into the presenter by calling `->getPresenter()` from the model.

**Example View**
```html
<ul>
@foreach ($recentTopics AS $topic)
	<li><a href="{{ $topic->url() }}">{{ $topic->title }}</a></li>
@endforeach
</ul>
```
