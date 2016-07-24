<?php namespace Moregold\Infrastructure;

use \ReflectionClass;

abstract class Enum
{
    /**
     * Verifies if child class contains static property of $name,
     * if exists and is array, check if the value is in the array
     *
     * @param  string  $value
     *
     * @return boolean
     */
    public function isValid($value)
    {
        $properties = $this->getStaticProps();
        foreach ($properties as $k => $v) {
            if ($v == $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attempt to access a static property of the child class
     *
     * @param  string $name
     * @param  mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            return static::${$name};
        }

        $properties = $this->getStaticProps();
        $values = [];

        foreach ($properties as $k => $v) {
            if (strpos($k, $name) === 0) {
                array_push($values, $v);
            }
        }

        if (count($values)) {
            return $values;
        }

        return null;
    }

    /**
     * Create new reflection class for current object
     *
     * @return ReflectionClass Reflection class for current object
     */
    private function reflect()
    {
        return new ReflectionClass(get_class($this));
    }

    /**
     * Retrieve array of static properties on current reflection class
     *
     * @return array Static properties
     */
    private function getStaticProps()
    {
        return $this->reflect()->getStaticProperties();
    }
}
