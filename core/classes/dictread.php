<?php

class DictRead implements GetParam {
	
	protected $_array = array();
	
	/*-------------------------------------------------------------------*/
	
	public function __construct($array=array()) {
		$this->_update($array);
	}
	
	/*-------------------------------------------------------------------*/
	
	public function __get($name) {
		return $this->get_item($name);
	}
	
	public function __toString() {
		return ArrayHelper::http_query($this->_array, true);
	}
	
	public function __toArray() {
		return $this->get_array();
	}
	
	/*-------------------------------------------------------------------*/
	
	protected function _set_item($key, $value=null) {
		$this->_array[$key] = $value;
		return $this;
	}
	
	
	protected function _remove_item($key) {
		unset($this->_array[$key]);
		return $this;
	}
	
	protected function _drop_item($key, $default=null) {
		$value = $this->_get_item($key, $default);
		$this->_remove_item($key);
		return $value;
	}
	
	protected function _clear() {
		$this->_array = array();
		return $this;
	}
	
	protected function _update($array=array()) {
		foreach((array) $array as $key => $value)
			$this->_set_item ($key, $value);		
		return $this;
	}
	
	protected function _set_array($array=array()) {
		$this->_clear();
		$this->_update($array);
		return $this;
	}
	
	/*-------------------------------------------------------------------*/
	
	public function get_item($key, $default=null) {
		return array_key_exists($key, $this->_array) ? $this->_array[$key] : $default;
	}
		
	public function get_array() {
		return array_merge(array(), $this->_array);
	}
	
	public function get_keys() {
		return array_keys($this->_array);
	}
	
	public function get_values() {
		return array_values($this->_array);
	}
	
	/*-------------------------------------------------------------------*/
	
	public function exists($key) {
		return $this->exists_key($key);
	}
	
	public function exists_value($value) {
		return in_array($value, $this->_array);
	}
	
	public function exists_key($key) {
		return array_key_exists($key, $this->_array);
	}
	
	
	public function has($key) {
		return $this->exists($key);
	}
	
	public function has_value($value) {
		return $this->exists_value($value);
	}
	
	public function has_key($key) {
		return $this->exists_key($key);
	}
	
	/*-------------------------------------------------------------------*/
	
	public function get_item_boolean($key, $default=null) {
		return CastHelper::to_bool($this->get_item($key, $default));
	}
	
	public function get_item_bool($key, $default=null) {
		return $this->get_item_boolean($key, $default);
	}
	
	public function get_item_string($key, $default=null) {
		return (string) $this->get_item($key, $default);
	}
	
	public function get_item_int($key, $default=null) {
		return (int) $this->get_item($key, $default);
	}
	
	public function get_item_float($key, $default=null) {
		return (float) $this->get_item($key, $default);
	}
	
	public function get_item_array($key, $default=null) {
		return (array) $this->get_item($key, $default);
	}
	
	public function get_item_raw($key, $default=null) {
		return $this->get_item($key, $default);
	}

	/*-------------------------------------------------------------*/

	public function get_param($name, $default=null)
	{
		if($this->has_key($name))
		{
			return $this->get_item($name);
		}
		else
		{
			return $default;
		}
	}

	public function get_params_array()
	{
		return $this->__toArray();
	}

}

