<?php

class Slide implements \ArrayAccess
{

    /** @var array */
    private $values = array();

    /** @var Slider */
    private $slider;

    public function __call($method, $args)
    {
        if (strpos($method, 'get') === 0) {
            $method = lcfirst(preg_replace('~^get~', '', $method));
            if ($this->offsetExists($method)) {
                return $this->offsetGet($method);
            }
        } else {
            if ($this->offsetExists($method)) {
                return $this->offsetGet($method);
            }
        }
        return null;
    }

    public function __construct($values, Slider $slider)
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