<?php namespace Moregold\Infrastructure\Helpers;

trait SubAttributesTrait{

	public function getSubAttribute($name)
	{   
		$realtion_name = $this->_attribute_relation_name;
		$value = $this->_attribute_relation_value_name;
		$attribute_value = $this->$realtion_name->where($this->_attribute_relation_key_name, $name)->first();
		return $attribute_value ? $attribute_value->$value : false;

	}

}
