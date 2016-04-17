<?php 

class HTMLControlStaticLibraryJQueryUI extends HTMLControlStaticLibrary {
	
	protected static $_theme_css;

	protected static function _get_default_theme_css() {
		return URLHelper::get_zframework_static_url('thirdparty/jquery-ui/themes/original/style.css');
	}
	
	/*-------------------------------------------------------------*/
	
	public static function set_theme_css($css_file) {
		self::$_theme_css = $css_file;
	}
	
	public static function get_theme_css() {
		return self::$_theme_css;
	}
	
	/*-------------------------------------------------------------*/
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/jquery-ui/jquery-ui.js'));
	}
	
	public function get_library_css_files() {
		$theme_css = self::$_theme_css ? self::$_theme_css : self::_get_default_theme_css();
		return array(URLHelper::get_zframework_static_url('thirdparty/jquery-ui/themes/original/style.css'), $theme_css);
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERY);
	}
}