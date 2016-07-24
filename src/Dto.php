<?php namespace Moregold\Infrastructure;

use Moregold\Infrastructure\Messages\ErrorMessageTrait,
    Moregold\Infrastructure\Messages\SuccessMessageTrait;

abstract class Dto
{
    use ErrorMessageTrait,
        SuccessMessageTrait;

    /**
     * Attributes to append to model during serialization
     *
     * @var array
     */
    protected $appends = [
        'hasErrors',
        'getErrorsAsString',
        'hasSuccesses',
        'getSuccessesAsString'
    ];

    /**
     * Add property to appends array
     *
     * @param string $append Property to add to appends array
     *
     * @return \Moregold\Infrastructure\Dto  Updated model
     */
    protected function addAppends($append)
    {
        if (!property_exists($this, 'appends') || !is_array($this->appends)) {
            $this->appends = [];
        }
        if (!empty($append) && !in_array($append, $this->appends)) {
            array_push($this->appends, $append);
        }
        return $this;
    }

    /**
     * Retrieves attributes to append to model during serialization
     *
     * @var array
     */
    public function getAppends()
    {
        return $this->appends;
    }

    /**
     * Set a bunch of attributes
     *
     * @param  array $attributes
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    protected function addAppendsGroup($relations)
    {
        if (!is_array($relations)) {
            $relations = [$relations];
        }
        $indices_to_remove = [];
        $prefix = 'appends';
        for ($i = 0; $i < count($relations); $i++) {
            $relation = $relations[$i];
            if (is_string($relations[$i]) && strrpos($relation, $prefix) === 0) {
                preg_match("/^".$prefix."([A-Za-z]+)$/", $relation, $matches);
                if (!empty($matches) && isset($matches[1])) {
                    $group = explode('group', strtolower($matches[1]));
                    if (count($group) == 2) {
                        $method = $group[0].'Group';
                        if (method_exists($this, $method)) {
                            $this->$method($group[1]);
                            array_push($indices_to_remove, $i);
                        }
                    }
                }
            }
        }
        foreach ($indices_to_remove as $i) {
            unset($relations[$i]);
        }
        return $relations;
    }

    public function mergeAttributes($attributes)
    {
        if (!is_array($attributes)) {
            $attributes = get_object_vars($attributes);
        }
        $merged = [];
        foreach (get_object_vars($this) as $key => $value) {
            $merged[$key] = $value;
            if (isset($attributes[$key]) && !is_null($attributes[$key])) {
                $merged[$key] = $attributes[$key];
            }
        }
        return $this->fill($merged);
    }
}
