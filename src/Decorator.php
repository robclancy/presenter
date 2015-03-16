<?php namespace Robbo\Presenter;

use ArrayAccess;
use IteratorAggregate;

class Decorator
{
    /*
     * If this variable implements Robbo\Presenter\PresentableInterface then turn it into a presenter.
     *
     * @param  mixed $value
     * @return mixed $value
    */
    public function decorate($value)
    {
        if ($value instanceof PresentableInterface) {
            return $value->getPresenter();
        }

        if (is_array($value) or ($value instanceof IteratorAggregate and $value instanceof ArrayAccess)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->decorate($v);
            }
        }

        return $value;
    }
}
