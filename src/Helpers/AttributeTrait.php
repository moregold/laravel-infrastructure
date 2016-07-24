<?php namespace Moregold\Infrastructure\Helpers;

trait AttributeTrait
{
    /**
     * Generate random integer
     *
     * @param  array $keys       Array of attributes to mass assign
     * @param  array $attributes Associative array of attributes
     *
     * @return void
     */
    public function massAssign($keys, $attributes)
    {
        foreach ($keys as $key) {
            if (isset($attributes[$key])) {
                $this->{$key} = strtolower($attributes[$key]);
            }
        }
    }
    /**
     * Iterates over array to set default keys to null if not currently provided
     *
     * @param array  $keys  Keys to check
     * @param array  $input Array to parse
     *
     * @return array       Affected array
     */
    public function setArrayDefaults($keys, $input)
    {
        foreach ($keys as $key) {
            if (!isset($input[$key])) {
                $input[$key] = null;
            }
        }
        return $input;
    }
}
