<?php

class CLIHelper {

	const STDERR = 'php://stderr';
	
	const STDOUT = 'php://stdout';
	
	/*-----------------------------------------------------------------------------*/
	
	private static $_COMMAND_LINE_ARGV = null;
	
	private static $_COMMAND_LINE_SCRIPT = null;
	
	private static function _test_argv() {
		
		if(is_null(self::$_COMMAND_LINE_ARGV)) {
			
			if(array_key_exists('argv', $_SERVER) && count($_SERVER['argv']) > 0) {
						
				self::$_COMMAND_LINE_ARGV = (array) array_slice($_SERVER['argv'], 1);

				self::$_COMMAND_LINE_SCRIPT = $_SERVER['argv'][0];

			} else {

				self::$_COMMAND_LINE_ARGV = array();
			}
		}
	}
	
	/*-----------------------------------------------------------------------------*/
	
	public static function get_script() {
		self::_test_argv();
		return self::$_COMMAND_LINE_SCRIPT;
	}
	
	public static function get_arg($index=0) {
		self::_test_argv();
		return ArrayHelper::get_item(self::$_COMMAND_LINE_ARGV, $index);
	}
	
	public static function get_args($offset=0, $length=null) {
		self::_test_argv();
		return array_slice(self::$_COMMAND_LINE_ARGV, $offset, $length);
	}
    
	public static function get_args_count() {
		self::_test_argv();
		return count(self::$_COMMAND_LINE_ARGV[$index]);
	}

	
	/*-----------------------------------------------------------------------------*/
	
	public static function write($out, $str) {
		
		$f = @ fopen($out, 'w+');
		fwrite($f, $str);
		@ fclose($f);
	}
	
	public static function write_line($out, $str='') {
		
		self::write($out, "{$str}\n");
	}
	
	public static function write_stdout($str='') {
		
		self::write(self::STDOUT, $str);
	}
	
	public static function write_line_stdout($str='') {
		
		self::write_line(self::STDOUT, $str);
	}
	
	public static function write_stderr($str='') {
		
		self::write(self::STDERR, $str);
	}
	
	public static function write_line_stderr($str='') {
		
		self::write_line(self::STDERR, $str);
	}
}