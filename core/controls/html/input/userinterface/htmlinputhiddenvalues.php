<?php 

class HTMLInputHiddenValues extends HTMLInputControl {
	
	protected $_values;
	
	public function __construct($values=null) {
		parent::__construct();
		$this->set_values(is_null($values) ? $_REQUEST : $values);
	}
	
	public function get_values() {
		return array_merge($this->_values, array());
	}

	public function set_values($value) {
		$this->_values = $value;
	}
	
	public function set_value($value) {
		$this->set_values(CastHelper::to_array($value));
	}
	
	public function get_value() {
		return $this->get_values();
	}

	
	public function prepare_params() {
		$values = ArrayHelper::encode_html($this->_values);
		$this->set_param('values', $values);
		parent::prepare_params();
	}
	
	

}