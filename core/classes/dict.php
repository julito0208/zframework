<?php

class Dict extends DictRead implements Params{
	
	
	public function __construct($array=array()) {
		parent::__construct();
		$this->update($array);
	}

	public function __set($name, $value)
	{
		$this->set_item($name, $value);
	}

	/*-------------------------------------------------------------*/
	
	public function set_item($key, $value=null) {
		return $this->_set_item($key, $value);
	}
		
	public function remove_item($key) {
		return $this->_remove_item($key);
	}
	
	public function drop_item($key, $default=null) {
		return $this->_drop_item($key, $default);
	}
	
	public function clear() {
		return $this->_clear();
	}
	
	public function update($array=array()) {
		return $this->_update($array);
	}
	
	public function set_array($array=array()) {
		return $this->_set_array($array);
	}

	/*-------------------------------------------------------------------*/
	
	public function set_item_boolean($key, $value=null) {
		return $this->set_item($key, (bool) $value);
	}
	
	public function set_item_bool($key, $value=null) {
		return $this->set_item($key, (bool) $value);
	}
	
	public function set_item_string($key, $value=null) {
		return $this->set_item($key, (string) $value);
	}
	
	public function set_item_int($key, $value=null) {
		return $this->set_item($key, (int) $value);
	}
	
	public function set_item_float($key, $value=null) {
		return $this->set_item($key, (float) $value);
	}
	
	public function set_item_array($key, $value=null) {
		return $this->set_item($key, (array) $value);
	}
	
	public function set_item_raw($key, $value=null) {
		return $this->set_item($key, $value);
	}

	/*-------------------------------------------------------------*/

	public function set_param($name, $value=null)
	{
		$this->set_item($name, $value);
		return $this;
	}

	public function has_param($name)
	{
		return $this->has_key($name);
	}

	public function remove_param($name)
	{
		$this->drop_item($name);
		return $this;
	}

}

