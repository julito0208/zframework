<?php 

class CastHelper {



	public static function is_instance_of($var, $classname) {
		return (is_object($var) && (($var_classname = get_class($var)) == $classname || is_subclass_of($var_classname, $classname)));
	}


	public static function is_not_null($value) {
		return !is_null($value);
	}

	//--------------------------------------------------------------------


	public static function to_array($var){

		if(is_array($var)) return $var;
		else if(is_object($var) && method_exists($var, '__toArray')) return (array) $var->__toArray();
		else return (array) $var;
	}


	public static function to_string($var){
		return strval($var);
	}


	public static function to_bool($var, $parse=true){

		if(is_bool($var)) {

			return $var;

		} else if($parse) {

			if($var == 'false') {

				return false;

			} else {

				return (boolean) $var;
			}

		} else {

			return (boolean) $var;
		}

	}



	public static function to_int($var){
		return (integer) $var;
	}


	public static function to_float($var){
		return (float) $var;
	}

	
	
}