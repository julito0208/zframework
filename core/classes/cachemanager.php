<?php

@ define('ZPHP_CACHE_ENABLED', true);
@ define('ZPHP_CACHE_SYSTEM', 'memcached');

//-------------------------------------------------------------------------------

class CacheManager {

	const SYSTEM_NONE = 'none';
	const SYSTEM_FILE = 'file';
	const SYSTEM_MEMCACHED = 'memcached';
	
	const SYSTEM_DEFAULT = self::SYSTEM_NONE;
	
	//-------------------------------------------------------------------------------
	
	private static $_cache_system = null;
	
	/* @return CacheSystem */
	private static function get_default_cache_system() {
		
		if(is_null(self::$_cache_system)) {

			if(ZPHP::get_config('cache_enabled')) {

				$system = strtolower(trim(ZPHP::get_config('cache_system')));
				
				switch($system) {
					
					case self::SYSTEM_FILE:
						self::$_cache_system = new CacheSystemFile();
					break;
					
					case self::SYSTEM_MEMCACHED:
						self::$_cache_system = new CacheSystemMemcached();
					break;
				
					default:
						self::$_cache_system = new CacheSystemNone();
					break;
					
				}
				
			} else {
				
				self::$_cache_system = new CacheSystemNone();
			}
			
		}
		
		return self::$_cache_system;
	}
	
	
	//-------------------------------------------------------------------------------
	
	public static function save($key, $value) {
		
		$cache_system = self::get_default_cache_system();
		return $cache_system->save($key, $value);
	}
	
	public static function delete($key) {
		
		$cache_system = self::get_default_cache_system();
		return $cache_system->delete($key);
	}
	
	public static function get($key) {
		
		$cache_system = self::get_default_cache_system();
		return $cache_system->get($key);
	}
	
	public static function exists($key) {
		
		$cache_system = self::get_default_cache_system();
		return $cache_system->exists($key);
	}
	
}