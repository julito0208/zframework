<?php //-----------------------------------------------------------------------


class HTMLInputSelectControl extends HTMLInputControl {
	
	const DEFAULT_OPTION_OPTIONAL_VALUE = "";
	const DEFAULT_OPTION_VALUE = "";
	
	//-----------------------------------------------------------------------
	
	protected $_options = array();
	protected $_defaultOptionEnabled = true;
	protected $_defaultOptionText = null;
	protected $_defaultOptionValue = null;
	
	
	public function __construct($id=null, $name=null, array $options=array()) {
				
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_options($options);
		
	}
	
	
	//-----------------------------------------------------------------------

	public function get_options() {
		return $this->_options;
	}
	
	
	/* @return HTMLInputSelectControl */
	public function add_option($option_label, $value=null) {
		
		$this->_options[] = JSONOptionItem::parse_option_array($option_label, $value);
		return $this;
	}

	
	/* @return HTMLInputSelectControl */
	public function clear_options() {
		$this->_options = array();
		return $this;
	}
	
	
	/* @return HTMLInputSelectControl */
	public function add_options(array $options) {
		
		foreach((array) $options as $option) {
			$this->add_option($option);
		}
		
		return $this;
	}
	
	/* @return HTMLInputSelectControl */
	public function set_options(array $options) {
		
		$this->clear_options();
		$this->add_options($options);
		return $this;
	}
	
	
	public function get_default_option_enabled() {
		return $this->_defaultOptionEnabled;
	}

	public function set_default_option_enabled($var) {
		$this->_defaultOptionEnabled = $var;
	}



	public function get_default_option_text() {
		return $this->_defaultOptionText;
	}

	public function set_default_option_text($var) {
		$this->_defaultOptionText = $var;
	}



	public function get_default_option_value() {
		return $this->_defaultOptionValue;
	}

	public function set_default_option_value($var) {
		$this->_defaultOptionValue = $var;
	}



	public function set_default_option($var, $value='') {
		
		
		if($var) {
			
			$this->set_default_option_enabled(true);
			
			if(is_string($var)) {
				
				$this->set_default_option_text($var);
			}
			
			if(func_num_args() > 1) {
			
				$this->set_default_option_value($value);
				
			}
			
		} else {
		
			$this->set_default_option_enabled(false);
			
		}
		
	}
	
	
	public function set_value($value) {
		
		if($value && $value instanceof OptionItem) {
			$value = $value->get_option_item_value();
		}
		
		return parent::set_value($value);
	}
	

	//-----------------------------------------------------------------------
	
	
	public function prepare_params() {
		
		
		parent::prepare_params();
		
		$this->set_param('options', $this->get_options());
		$this->set_param('default_option_enabled', $this->get_default_option_enabled());
		$this->set_param('default_option_text', $this->get_default_option_enabled() ? ( $this->get_default_option_text() ? $this->get_default_option_text() : ($this->get_optional() ? String::get('not_specified') : String::get('select')) ) : '');
		$this->set_param('default_option_value', '');
		

	}
	
}


//----------------------------------------------------------------------- ?>