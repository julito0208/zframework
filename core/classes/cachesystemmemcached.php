<?php

@ define('ZPHP_CACHE_SYSTEM_MEMCACHED_HOST', '127.0.0.1');
@ define('ZPHP_CACHE_SYSTEM_MEMCACHED_PORT', '11211');
@ define('ZPHP_CACHE_SYSTEM_MEMCACHED_TIMEOUT', '30');

//-------------------------------------------------------------------------------

class CacheSystemMemcached implements CacheSystem {


	private static function _parse_key($key) {
		return preg_replace('#\W+#', '', $key);
	}
	
	//-------------------------------------------------------------------------------
	
	private $_host;
	private $_port;
	private $_timeout;
	private $_memcached;
	private $_connected = false;
	
	public function __construct($host=null, $port=null, $timeout=null) {
		
		$this->_host = $host ? $host : constant('ZPHP_CACHE_SYSTEM_MEMCACHED_HOST');
		$this->_port = $port ? $port : constant('ZPHP_CACHE_SYSTEM_MEMCACHED_PORT');
		$this->_timeout = $timeout ? $timeout : constant('ZPHP_CACHE_SYSTEM_MEMCACHED_TIMEOUT');
		
		try {
			
			$this->_memcached = new Memcache();

			$this->_connected = $this->_memcached->connect($this->_host, $this->_port, $this->_timeout);
			
		} catch(Exception $ex) {}
		
		if(!$this->_connected) {
			@ error_log("No se pudo conectar al server memcached: {$this->_host}:{$this->_port}");
		}
	}
		
	//-------------------------------------------------------------------------------
	
	public function save($key, $value) {
		
		if($this->_connected) {
			
			$key = self::_parse_key($key);
			$this->_memcached->set($key, $value);
		}
		
	}
	
	public function delete($key) {
		
		if($this->_connected) {
			
			$key = self::_parse_key($key);
			$this->_memcached->delete($key);
		}
		
	}

	
	public function get($key) {
		
		if($this->_connected) {
			
			$key = self::_parse_key($key);
			return $this->_memcached->get($key);
			
		} else {
			
			return null;
			
		}
				
	}
	
	public function exists($key) {
		
		if($this->_connected) {
			
			$key = self::_parse_key($key);
			return !is_null($this->_memcached->get($key));
			
		} else {
			
			return false;
			
		}
		
		
	}
	
}
