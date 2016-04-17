<?php

class HTTPCookie {
	
	private static $_VARS = null;
	
	/* @return DictRead */
	public static function get_dict() {
		if(is_null(self::$_VARS)) {
			self::$_VARS = new DictRead($_COOKIE);
		}
		
		return self::$_VARS;
	}
	
	/*-------------------------------------------------*/
	
	
	public static function get_item($key, $default=null) {
		return self::get_dict()->get_item($key, $default);
	}
		
	public static function get_array() {
		return self::get_dict()->get_array();
	}
	
	public static function get_keys() {
		return self::get_dict()->get_keys();
	}
	
	public static function get_values() {
		return self::get_dict()->get_values();
	}
	
	/*-------------------------------------------------------------------*/
	
	public static function exists($key) {
		return self::get_dict()->exists($key);
	}
	
	public static function exists_value($value) {
		return self::get_dict()->exists_value($value);
	}
	
	public static function exists_key($key) {
		return self::get_dict()->exists_key($key);
	}
	
	
	public static function has($key) {
		return self::get_dict()->has($key);
	}
	
	public static function has_value($value) {
		return self::get_dict()->has_value($value);
	}
	
	public static function has_key($key) {
		return self::get_dict()->has_key($key);
	}
	
	/*-------------------------------------------------------------------*/
	
	public static function get_item_boolean($key, $default=null) {
		return self::get_dict()->get_item_boolean($key, $default);
	}
	
	public static function get_item_bool($key, $default=null) {
		return self::get_dict()->get_item_bool($key, $default);
	}
	
	public static function get_item_string($key, $default=null) {
		return self::get_dict()->get_item_string($key, $default);
	}
	
	public static function get_item_int($key, $default=null) {
		return self::get_dict()->get_item_int($key, $default);
	}
	
	public static function get_item_float($key, $default=null) {
		return self::get_dict()->get_item_float($key, $default);
	}
	
	public static function get_item_array($key, $default=null) {
		return self::get_dict()->get_item_array($key, $default);
	}
	
	public static function get_item_raw($key, $default=null) {
		return self::get_dict()->get_item_raw($key, $default);
	}
	
}