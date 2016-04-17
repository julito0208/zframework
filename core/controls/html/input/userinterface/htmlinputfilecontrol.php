<?php //-----------------------------------------------------------------------


class HTMLInputFileControl extends HTMLInputControl {
	
	protected $_multiple = false;

	public function __construct($id=null, $name=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
	}

	public function get_multiple() {
		return $this->_multiple;
	}

	public function set_multiple($value) {
		$this->_multiple = $value;
	}


	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('multiple', $this->_multiple);
	}
	
}


//----------------------------------------------------------------------- ?>