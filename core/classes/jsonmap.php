<?php


class JSONMap extends Dict implements MimeControl {

	public static function serialize($var, $force_obj=false) {
		if($force_obj) return JSHelper::cast_obj(CastHelper::to_array($var));
		else return JSHelper::cast($var);
	}

	public static function unserialize($var, $force_obj=false){

		$json = array();

		if(!is_array($var) && !(is_object($var) && get_class($var) == 'stdClass')) {

			$var = json_decode($var);

		}

		if(is_array($var)) {

			foreach($var as $key => $value) {
				$json[$key] = $value;
			}

		} else if(is_object($var) && get_class($var) == 'stdClass') {

			foreach($var as $key => $value) {
				$json[$key] = $value;
			}
		}

		foreach($json as $key => $value) {

			if(is_array($value) || (is_object($value) && get_class($value) == 'stdClass')) {
				$json[$key] = JSONMap::unserialize($value);
			}

		}

		return $json;

	}

	/* @return JSON */
	public static function parse($var, $force_obj=false){

		$array = self::unserialize($var, $force_obj);

		if($array)
		{
			return new JSONMap($array);
		}
		else
		{
			return new JSONMap();
		}

	}

	public static function from_file($path, $force_obj=false)
	{
		$string = file_get_contents($path);
		return self::parse($string, $force_obj);
	}


	//--------------------------------------------------------------------------------------------------


	public static function out_var($var, $charset=null, $force_obj=false) {

		if(!$charset) $charset = ZPHP::get_config('charset');

		$serialized = JSONMap::serialize($var, $force_obj);
		@ header('Content-Type: application/json; charset="'.$charset.'"');
		@ header('Content-Length: '.strlen($serialized));

		echo $serialized;

		die();

	}


	/*----------------------------------------------------------------------------------*/

	protected static $_CHARSET = null;

	public static function get_default_charset() {

		if(self::$_CHARSET) return self::$_CHARSET;
		else return ZPHP::get_config('charset');
	}

	public static function set_default_charset($charset) {

		self::$_CHARSET = $charset ? $charset : ZPHP::get_config('charset');

	}

	/*----------------------------------------------------------------------------------*/

	protected $_charset = null;

	public function __construct($array=array(), $charset=null) {
		parent::__construct($array);
		if(!is_null($charset)) $this->set_charset ($charset);
	}


	public function set_charset($charset=null) {
		$this->_charset = $charset ? $charset : self::get_default_charset();
		return $this;
	}

	public function get_charset() {
		return $this->_charset ? $this->_charset : self::get_default_charset();
	}


	public function out() {
		JSONMap::out_var($this->get_array(), $this->get_charset(), true);
		die();
	}

	public function save_to($filename) {
		@ file_put_contents($filename, JSONMap::serialize($this->get_array(), true));
	}

	public function out_attachment($filename=null) {
		NavigationHelper::header_content_attachment($filename ? $filename : 'json.json');
		$this->out();
	}
}
