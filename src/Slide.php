<?php

class Slide implements ArrayAccess
{

    /** @var array */
    private $values = array();

    /** @var Impress */
    private $slider;

    public function __call($method, $args)
    {
        if (strpos($method, 'get') === 0) {
            $method = lcfirst(preg_replace('~^get~', '', $method));
            if (isset($this->values[$method])) {
                return $this->values[$method];
            }
        } else {
            if (isset($this->values[$method])) {
                return $this->values[$method];
            }
        }
        return null;
    }

    public function __construct($values, Impress $slider)
    {
        $this->values = $values;
        $this->slider = $slider;
    }

    public function getSlider()
    {
        return $this->slider;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->values);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}