<?php //-----------------------------------------------------------------------


class HTMLInputDateTimeControl extends HTMLInputControl {
	

	protected $_placeholder;
	protected $_time_enabled;
	
	public function __construct($id=null, $name=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_value(Date::now());
		self::add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_DATE_INPUT);
	}

	
	public function get_placeholder() {
		return $this->_placeholder;
	}

	public function set_placeholder($value) {
		$this->_placeholder = $value;
		return $this;
	}
	
	
	public function get_time_enabled() {
		return $this->_time_enabled;
	}

	public function set_time_enabled($value) {
		$this->_time_enabled = $value;
		return $this;
	}
	
	
	public function set_value($value) {
		$this->_value = Date::parse($value);
	}

	
	public function prepare_params() {
		
		parent::prepare_params();
		
		$this->set_param('placeholder', $this->_placeholder);
		$this->set_param('time', $this->_time);
		
		if(!$this->get_title()) $this->set_param('title', $this->_placeholder);
		
		$this->set_param('value', $this->_value->format_sql_datetime());
	}
	
}


//----------------------------------------------------------------------- ?>