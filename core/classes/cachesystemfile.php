<?php

class CacheSystemFile implements CacheSystem {

	private static function _variable_save_to_file($filename, $variable) {
		$serialized = serialize($variable);
		$result = @ file_put_contents($filename, $serialized);
		return $result === false ? false : true;
	}

	private static function _variable_load_from_file($filename) {
		$contents = @ file_get_contents($filename);
		if($contents)
		{
			return unserialize($contents);
		}
		else
		{
			return null;
		}
	}

	//-------------------------------------------------------------------------------

	private $_cache_dir;
	
	public function __construct() {
		$this->_cache_dir = ZPHP::get_config('cache_system_file_dir');
		@ mkdir($this->_cache_dir, 0777, true);
	}
		
	//-------------------------------------------------------------------------------
	
	private function _get_key_path($key) {
		$key = preg_replace('#\W+#', '', $key);
		return $this->_cache_dir.'/'.$key;
	}
	
	//-------------------------------------------------------------------------------
	
	public function save($key, $value) {
		$filename = $this->_get_key_path($key);
		self::_variable_save_to_file($filename, $value);
	}
	
	public function delete($key) {
		$filename = $this->_get_key_path($key);
		@ unlink($filename);
	}
	
	public function get($key) {
		$filename = $this->_get_key_path($key);
		return self::_variable_load_from_file($filename);
	}
	
	public function exists($key) {
		$filename = $this->_get_key_path($key);
		return file_exists($filename);
	}
	
}
