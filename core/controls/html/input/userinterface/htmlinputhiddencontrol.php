<?php //-----------------------------------------------------------------------


class HTMLInputHiddenControl extends HTMLInputControl {
	

	public function __construct($id=null, $name=null, $value=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_value($value);
	}

	
		
	public function prepare_params() {
		
		parent::prepare_params();
		
	}
	
}


//----------------------------------------------------------------------- ?>