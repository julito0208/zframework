<?php //-----------------------------------------------------------------------


class HTMLInputCheckboxControl extends HTMLInputControl {
	
	
	protected $_checked;
	
	
	public function __construct($id=null, $name=null, $value=null) {
				
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_value($value);
		
	}
	
	public function get_checked() {
		return $this->_checked;
	}

	public function set_checked($value) {
		$this->_checked = $value;
		return $this;
	}
	
	public function set_value($value) {
		if($value && $value instanceof OptionItem) {
			parent::set_value($value->get_option_item_value());
			$this->set_title($value->get_option_item_label());
			
		} else {
			
			parent::set_value($value);
		}
		
		return $this;
	}

	
	//-----------------------------------------------------------------------

	
	public function prepare_params() {
		
		$this->set_param('checked', $this->_checked);
		parent::prepare_params();

	}
	
}


//----------------------------------------------------------------------- ?>