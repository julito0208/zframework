<?php

class ClassHelper {

	private static $_CLASSES_PATHS = array();
	
	private static $_CLASSES_SEARCH_DIRS = null;
	private static $_CLASSES_SEARCH_FORMATS = null;
	
	private static $_DEFAULT_CLASSES_SEARCH_FORMATS = array(
		'%s.php', 
		'controller/%s.php',
		'controllers/%s.php',
		'userinterface/%s.php',
	);
	
	private static $_DEFAULT_CLASSES_SEARCH_DIRS = array(
		'core/classes', 
		'core/interfaces', 
		'core/entities',
		'core/services',
		'core/controls/ajax', 
		'core/controls/*/controller',
		'core/controls/*/controllers',
		'core/controls/*/userinterface',
		'core/controls/html/*/controller',
		'core/controls/html/*/controllers',
		'core/controls/html/*/userinterface',
		'core/controls/text/*/controller',
		'core/controls/text/*/controllers',
		'core/controls/text/*/userinterface',
		'core/controls/images',
		'core/toolpages/controller',
		'core/toolpages/controllers',
		'core/toolpages/userinterface',
		'core/htmllibraries',
	);
	
	/*----------------------------------------------------------------------------*/


	protected static function _dir_list($dirname, $filter_callback=null)
	{
		$dir_resource = opendir($dirname);

		$contents_files = array();
		$contents_dirs = array();

		while(($content = readdir($dir_resource))) {

			if($content != '.' && $content != '..') {

				$content_full_path = rtrim($dirname,'/').'/'.$content;
				$contents_array_name = is_dir($content_full_path) ? 'contents_dirs' : 'contents_files';

				$value = $content_full_path;

//				if(self::FILESYSTEM_NAMES_UTF8)
				if(true)
				{
					$value = utf8_decode($value);
				}


				if(!$filter_callback || call_user_func($filter_callback, $content_full_path))
					${$contents_array_name}["{$value}"] = strtolower($value);
			}
		}

		closedir($dir_resource);
		asort($contents_dirs);
		asort($contents_files);

		return array_merge(array_keys($contents_dirs), array_keys($contents_files));
	}

	protected static function _get_paths_from_expression($arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$paths = array();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				$paths = array_merge($paths, call_user_func_array(array('self', '_get_paths_from_expression'), $arg));
			}
			else
			{
				if(strpos($arg, '*') !== false)
				{
					list($first_part, $second_part)  = explode('*', $arg, 2);

					if(strrpos($first_part, '/') === strlen($first_part) - 1)
					{
						$folder = rtrim($first_part, '/');
						$basename_search = '';

					}
					else
					{
						$folder = dirname($first_part);
						$basename_search = basename($first_part);
					}

					$contents = self::_dir_list($folder, function($path) use ($basename_search) {

						if($basename_search == '' || stripos(basename($path), $basename_search) === 0)
						{
							return true;
						}

						return false;
					});

					foreach($contents as $path)
					{
						$paths = array_merge($paths, self::_get_paths_from_expression($path.$second_part));
					}
				}
				else
				{
					$paths[] = $arg;
				}
			}
		}

		return $paths;
	}

	/*----------------------------------------------------------------------------*/

	public static function get_class_path($classname) {

		if(is_null(self::$_CLASSES_SEARCH_DIRS)) {

			$paths = array_filter((array) ZPHP::get_config('autoload_classes_dir', array()));
			$paths[] = ZPHP::get_config('db_entities_dir');
			
			
			foreach(self::$_DEFAULT_CLASSES_SEARCH_DIRS as $dir) {
				$paths[] = rtrim(ZPHP::get_config('zframework_dir'), '/').'/'.$dir;
			}

			self::$_CLASSES_SEARCH_DIRS = self::_get_paths_from_expression($paths);
			self::$_CLASSES_SEARCH_DIRS = array_unique(self::$_CLASSES_SEARCH_DIRS);
		}

		if(is_null(self::$_CLASSES_SEARCH_FORMATS)) {
			
			$formats = array_filter((array) ZPHP::get_config('autoload_classes_format', array()));
			
			foreach(self::$_DEFAULT_CLASSES_SEARCH_FORMATS as $format) {
				$formats[] = $format;
			}
			
			self::$_CLASSES_SEARCH_FORMATS = array_unique($formats);
			
		}

		if(!array_key_exists($classname, self::$_CLASSES_PATHS)) {
			
			self::$_CLASSES_PATHS[$classname] = null;
			
			$test_paths = array();
			
			foreach(self::$_CLASSES_SEARCH_DIRS as $dir) {
				
				foreach(self::$_CLASSES_SEARCH_FORMATS as $format) {

					$test_paths[] = rtrim($dir, '/').'/'.sprintf($format, $classname);
					$test_paths[] = rtrim($dir, '/').'/'.sprintf($format, strtolower($classname));
					
				}
			}
			
			foreach($test_paths as $path) {
				
				if(file_exists($path)) {
					self::$_CLASSES_PATHS[$classname] = $path;
					break;
				}
			}
			
		}

		return self::$_CLASSES_PATHS[$classname];
		
	}
	
	
	/*----------------------------------------------------------------------------*/

	public static function create_instance_array($classname, $args=array()) {

		$args_str_array = array();
		
		for($i=0; $i<count($args); $i++) {
			$args_str_array[] = "\$args[{$i}]";
		}
		
		$args_str = implode(', ', $args_str_array);
		$inst = null;
		
		$classname = preg_replace('#[^A-Za-z0-9\_\-]+#', '', $classname);

		if($classname == 'HTMLControlStaticLibraryFontsAwesome')
		{
			$path = self::get_class_path($classname);

		}
		eval("\$inst = new {$classname}({$args_str});");
		
		return $inst;

	}


	public static function create_instance($classname, $arg1=null, $arg2=null) {

		$args = array();

		for($i=1; $i<func_num_args(); $i++) {
			$args[] = func_get_arg($i);
		}

		return self::create_instance_array($classname, $args);
	}

	
	/*----------------------------------------------------------------------------*/
	
	public static function class_is_defined($classname) {
		$classes = get_declared_classes();
		$classes = explode(',', strtolower(implode(',', $classes)));
		return in_array(strtolower($classname), $classes);
	}

	
	public static function is_class($classname) {
		return self::class_is_defined($classname);
	}
	
	public static function interface_is_defined($classname) {
		$classes = get_declared_interfaces();
		$classes = explode(',', strtolower(implode(',', $classes)));
		return in_array(strtolower($classname), $classes);
	}

	
	public static function is_interface($classname) {
		return self::interface_is_defined($classname);
	}
	
	public static function is_instance_of($var, $classname) {
		
		return (is_object($var) && (($var_classname = get_class($var)) == $classname || is_subclass_of($var, $classname)));
		
	}
	
	/*----------------------------------------------------------------------------*/
	
	public static function get_class_constant($classname, $constant, $default=null) {
	
		$classname = preg_replace('#[^\\w]+#', '', (string) $classname);
		$constant = preg_replace('#[^\\w]+#', '', (string) $constant);
		
		try { eval("\$value = {$classname}::{$constant};"); }
		catch(Exception $ex) { var_export($ex); $value = $default; }
		
		return $value;
		
	}
	
	/*----------------------------------------------------------------------------*/
	
	public static function get_file_classes($path) {
		
		$classes = array_merge(get_declared_classes(), get_declared_interfaces());
		@ include_once($path);
		return array_diff(array_merge(get_declared_classes(), get_declared_interfaces()), $classes);

	}

	public static function class_method_exists($classname, $method)
	{

		if(method_exists($classname, $method))
		{
			return true;
		}
		else
		{
			$parent = get_parent_class($classname);

			if($parent)
			{
				return self::class_method_exists($parent, $method);
			}
			else
			{
				return false;
			}
		}
	}
}