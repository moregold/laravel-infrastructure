<?php namespace Moregold\Infrastructure\Helpers;

trait SubAttributesTrait{

	public function getSubAttribute($name)
	{   
		$realtion_name = $this->_attribute_relation_name;
		$value = $this->_attribute_relation_value_name;
		$attribute_value = $this->$realtion_name->where($this->_attribute_relation_key_name, $name)->first();
		return $attribute_value ? $attribute_value->$value : false;
	}

	/**
	 *
	 * Get sub attribute value, and create a new record if there is no record
	 */
	public function getSubAttributeWithDefault($attribute_name = '', $default_value = '')
	{
		$result = $this->getSubAttribute( $attribute_name );
		if( $result === false ) {
			$realtion_name = $this->_attribute_relation_name;
			$this->$realtion_name()->create([
				$this->_attribute_relation_key_name   => $attribute_name,
				$this->_attribute_relation_value_name => $default_value,
			]);
			$result = $default_value;
		}
		return $result;
	}

}
