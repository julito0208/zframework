<?php //-----------------------------------------------------------------------


class HTMLInputPasswordControl extends HTMLInputControl {
	

	protected $_placeholder;

	
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

	public function prepare_params() {
		
		parent::prepare_params();
		
		$this->set_param('placeholder', $this->_placeholder === true ? $this->get_title() : $this->_placeholder);

		if(!$this->get_title()) $this->set_param('title', $this->_placeholder);
	}
	
}


//----------------------------------------------------------------------- ?>