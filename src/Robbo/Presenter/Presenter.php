<?php namespace Robbo\Presenter;

use ArrayAccess;
use IteratorAggregate;

abstract class Presenter implements \ArrayAccess {

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
	 * Get the object we are wrapping.
	 * 
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->object;
	}

	/*
	 * This will be called when isset() is called via array access.
	 *
	 * @param  mixed $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		// We only check isset on the array, if it is an object we return true as the object could be overloaded
		if (is_array($this->object))
		{
			return isset($this->object[$offset]);
		}

		return true;
	}

	/**
	 * Add ability to access properties like an array.
	 *
	 * @param  mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * Set variable or key value using array access.
	 *
	 * @param  mixed $offset
	 * @param  mixed $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		if (is_array($this->object))
		{
			$this->object[$offset] = $value;
			return;
		}

		$this->object->$offset = $value;
	}

	/**
	 * Unset a variable or key value using array access.
	 *
	 * @param  mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		if (is_array($this->object))
		{
			unset($this->object[$offset]);
			return;
		}

		unset($this->object->$offset);
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
			return $this->$method();
		}

		$value = is_array($this->object) ? $this->object[$var] : $this->object->$var;

		return static::makePresentable($value);
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
		if (is_object($this->object))
		{
			$value = call_user_func_array(array($this->object, $method), $arguments);

			return static::makePresentable($value);
		}

		throw new \BadMethodCallException("Method {$method} does not exist.");
	}

	/**
	 * If this variable implements Robbo\Presenter\PresentableInterface then turn it into a presenter.
	 *
	 * @param  mixed $value
	 * @return mixed $value
	*/
	public static function makePresentable($value)
	{
		if ($value instanceof PresentableInterface)
		{
			return $value->getPresenter();
		}

		if (is_array($value) or ($value instanceof IteratorAggregate and $value instanceof ArrayAccess))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = static::makePresentable($v);
			}
		}

		return $value;
	}

}
