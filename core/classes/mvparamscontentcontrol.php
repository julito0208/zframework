<?php

abstract class MVParamsContentControl extends MVParamsControl {

	protected $_ob_started = false;

	
	public function ob_start() {
		if(!$this->_ob_started) {
			
			$this->_ob_started = true;
			ob_start();
		}
	}
	
	public function ob_started() {
		return $this->_ob_started;
	}
	
	public function ob_end() {
		if($this->_ob_started) {
			$this->_ob_started = false;
			$this->set_content(ob_get_clean());
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected function _get_parse_content() {
		$this->ob_end();
		return $this->_content;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function __set($name, $value=null) { 
		if($name == 'content') {
			$this->set_content($value);
		} else {
			return parent::__set($name, $value);
		}
	}
	
	public function __get($name) { 
		if($name == 'content') {
			return $this->get_content();
		} else {
			return parent::__get($name);
		}
	}
	
	public function __unset($name) { 
		if($name == 'content') {
			$this->clear_content();
		} else {
			return parent::__unset($name);
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function add_content($content) {
		$this->_content.= (string) $content;
	}
	
	public function get_content() {
		return $this->_content;
	}

	public function set_content($content) {
		$this->_content = (string) $content;
	}
	
	public function clear_content() {
		$this->set_content('');
	}

}