<?php //-----------------------------------------------------------------------


class HTMLInputTextNumericControl extends HTMLInputTextControl {
	
	protected $_signed;
	protected $_zero = true;
	protected $_float;
	
	public function __construct($id=null, $name=null) {

		parent::__construct();
		
		$this->set_id($id);
		$this->set_name($name);
		
		HTMLControl::add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_NUMERIC_FIELD);
	}

	
	public function get_signed() {
		return $this->_signed;
	}

	public function set_signed($value) {
		$this->_signed = $value;
		if($this->_signed) $this->set_zero(true);
		return $this;
	}

	public function get_float() {
		return $this->_float;
	}

	public function set_float($value) {
		$this->_float = $value;
		return $this;
	}

	

	public function get_zero() {
		return $this->_zero;
	}

	public function set_zero($value) {
		$this->_zero = $value;
		return $this;
	}

	

	
	public function prepare_params() {
		
		$this->set_param('signed', $this->_signed);
		$this->set_param('zero', $this->_zero);
		$this->set_param('float', $this->_float);
		parent::prepare_params();
	}
	
}


//----------------------------------------------------------------------- ?>