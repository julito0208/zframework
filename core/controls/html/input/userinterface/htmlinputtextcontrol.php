<?php //-----------------------------------------------------------------------


class HTMLInputTextControl extends HTMLInputControl {
	

	protected $_placeholder;
	protected $_autocomplete = true;
	protected $_sufix;
	protected $_prefix;
	
	
	public function __construct($id=null, $name=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
	}

	
	public function get_placeholder() {
		return $this->_placeholder;
	}

	public function set_placeholder($value) {
		$this->_placeholder = $value;
		return $this;
	}
	
	
	public function set_title_placeholder($value) {
		$this->set_placeholder($value);
		$this->set_title($value);
		return $this;
	}

	
	public function get_autocomplete() {
		return $this->_autocomplete;
	}

	public function set_autocomplete($value) {
		$this->_autocomplete = $value;
		return $this;
	}

	
	public function get_sufix() {
		return $this->_sufix;
	}

	public function set_sufix($value) {
		$this->_sufix = $value;
		return $this;
	}


	public function get_prefix() {
		return $this->_prefix;
	}

	public function set_prefix($value) {
		$this->_prefix = $value;
		return $this;
	}

	public function prepare_params() {
		
		parent::prepare_params();

		$this->set_param('placeholder', $this->_placeholder === true ? $this->get_title() : $this->_placeholder);
		$this->set_param('autocomplete', $this->_autocomplete);
		$this->set_param('sufix', $this->_sufix);
		$this->set_param('prefix', $this->_prefix);
		
		if(!$this->get_title()) $this->set_param('title', $this->_placeholder);
	}
	
}


//----------------------------------------------------------------------- ?>