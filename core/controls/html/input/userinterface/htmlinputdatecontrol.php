<?php //-----------------------------------------------------------------------


class HTMLInputDateControl extends HTMLInputDateTimeControl {
	

	
	public function __construct($id=null, $name=null) {

		parent::__construct($id, $name);
		
		$this->set_time_enabled(false);
		
		
	}
	
}


//----------------------------------------------------------------------- ?>