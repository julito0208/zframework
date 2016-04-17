<?php

abstract class MVParamsControl extends MVControl {

	protected $_params = array();
	protected $_original_params = array();
	
	public function __construct($params=null) {

		parent::__construct();
		
		$args = func_get_args();
		call_user_func_array(array($this, 'update_params'), $args);
	}

	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected function _get_parse_vars() {
		$this->_original_params = array_merge(array(), $this->_params);
		$this->prepare_params();
		$return_params = $this->get_params_array();
		$this->_params = $this->_original_params;
		$this->_original_params = array();
		return $return_params;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function __toArray() { 
		return $this->get_params_array(); 
	}
	
	public function __set($name, $value=null) {
		return $this->set_param($name, $value);
	}
	
	public function __get($name) {
		return $this->get_param($name);
	}
	
	public function __isset($name) { 
		return $this->has_param($name); 
	}
	
	public function __unset($name) { 
		return $this->remove_param($name); 
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
		
	public function set_param($name, $value=null) {
		$this->update_params(array($name => $value));
	}
	
	
	public function get_param($name, $default=null) {
		return array_key_exists($name, $this->_params) ? $this->_params[$name] : $default;
	}
		
	
	
	public function has_param($name) { 
		return array_key_exists($name, $this->_params); 
	}
	
	
	public function remove_param($name) {
		foreach(func_get_args() as $names)
			foreach((array) $names as $name)
				unset($this->_params[$name]);
	}
	
	public function clear_params() {
		$this->_params = array();
	}
	
	public function update_params($params=null){
		foreach(func_get_args() as $params)
			if($params)
				foreach(CastHelper::to_array($params) as $name => $value)
					if(is_null($value)) unset($this->_params[$name]);
					else $this->_params[$name] = $value;
	}
	
	
	public function param($arg1, $arg2=null) {
		
		$num_args = func_num_args();
		
		if($num_args == 1 && is_array($arg1)) return $this->update_params($arg1);
		
		else if($num_args == 1) return $this->get_param($arg1);
		
		else return $this->set_param($arg1, $arg2);
	}
	
		
	public function set_default_param($name, $value=null) {
		return $this->update_default_params(array($name => $value));
	}

	public function update_default_params($params=null){
		
		foreach(func_get_args() as $params)
			if($params)
				foreach((array) $params as $name => $value)
					if(!array_key_exists($name, $this->_params) && !is_null($value)) $this->_params[$name] = $value;
					else if(is_null($value)) unset($this->_params[$name]);
	}
			
		
	public function set_params($params=null) {
		
		$this->clear_params();
		
		$args = func_get_args();
		call_user_func_array(array($this, 'update_params'), $args);
	}
	
	
	public function get_params_array() {
		return array_merge($this->_params, array());
	}
	
	
	/* Util para soobrescribir */
	public function prepare_params() {}

}