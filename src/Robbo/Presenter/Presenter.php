<?php namespace Robbo\Presenter;

abstract class Presenter implements \ArrayAccess {

    /**
     * The object injected on Presenter construction.
     *
     * @var mixed
     */
    protected $object;

    /**
     * The decorator instance so we can nest presenters. Underscores here to avoid conflicts
     * if a presenter or object has "decorator" as a variable.
     *
     * @var \Robbo\Presenter\Decorator
     */
    protected static $__decorator;

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
     * Get the decorator, if none exists then use the default. Underscores here to avoid conflicts
     * if a presenter or object needs to use "getDecorator".
     *
     * @var \Robbo\Presenter\Decorator
     */
    protected function __getDecorator()
    {
        if (is_null(static::$__decorator))
        {
            static::$__decorator = new Decorator;
        }

        return static::$__decorator;
    }

    /**
     * This is so you can extend the decorator and inject it into the presenter at the class level so the
     * new decorator will be used for nested presenters. Method name should be "setDecorator" however 
     * like above I want to make conflicts less likely.
     *
     * @param  \Robbo\Presenter\Decorator
     * @return void
     */
    public static function setExtendedDecorator(Decorator $decorator)
    {
        static::$__decorator = $decorator;
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
        if ( ! is_array($this->object)) return true;

        if ($method = $this->getPresentMethodForVariable($offset))
        {
            $result = $this->$method();
            return isset($result);
        }

        return isset($this->object[$offset]);
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
        if ($method = $this->getPresentMethodForVariable($var))
        {
            return $this->$method();
        }

        return $this->__getDecorator()->decorate(is_array($this->object) ? $this->object[$var] : $this->object->$var);
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

            return $this->__getDecorator()->decorate($value);
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    /**
     * Allow ability to run isset() on a variable
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        if ($method = $this->getPresentMethodForVariable($name))
        {
            $result = $this->$method();
            return isset($result);
        }

        if (is_array($this->object))
        {
            return isset($this->object[$name]);
        }

        return isset($this->object->$name);
    }

    /**
     * Allow to unset a variable through the presenter
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if (is_array($this->object))
        {
            unset($this->object[$name]);
            return;
        }

        unset($this->object->$name);
    }

    /**
     * Fetch the 'present' method name for the given variable.
     *
     * @param  string       $variable
     * @return string|null
     */
    protectd function getPresentMethodForVariable($variable)
    {
        $method = 'present'.str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $variable)));
        if (method_exists($this, $method))
        {
            return $method;
        }
    }

}
