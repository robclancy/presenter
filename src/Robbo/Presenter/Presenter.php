<?php namespace Robbo\Presenter;

abstract class Presenter {

	/**
	 * The object injected on Presenter construction.
	 *
	 * @var mixed
	 */
	protected $object;

	/**
	 * Create the Presenter and store the object we are presenting.
	 *
	 * @param mixed $object
	 */
	public function __construct($object)
	{
		$this->object = $object;
	}

	/**
	 * Pass any unknown varible calls to present{$variable} or fall through to the injected object.
	 *
	 * @param  string $var
	 * @return mixed
	 */
	public function __get($var)
	{
		$method = 'present'.str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $var)));
		if (method_exists($this, $method))
		{
			return $this->$method;
		}

		return $this->object->$var;
	}

	/**
	 * Pass any uknown methods through to the inject object.
	 *
	 * @param  string $method
	 * @param  array  $arguments
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->object, $method), $arguments);
	}
}