<?php //----------------------------------------------------------------------


abstract class SmartObject {

	public function __get($name) {

		$method_name = "get_{$name}";

		if(method_exists($this, $method_name)) {
			return $this->$method_name();
		}
			
	}


	public function __set($name, $value) {

		$method_name = "set_{$name}";

		if(method_exists($this, $method_name)) {
			return $this->$method_name($value);
		}
	}
	
}
