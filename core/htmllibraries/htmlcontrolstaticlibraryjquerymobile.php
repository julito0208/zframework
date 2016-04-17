<?php 

class HTMLControlStaticLibraryJQueryMobile extends HTMLControlStaticLibrary {
	
	private static $_VERSION = '1.4.5';
	
	public function get_library_js_files() {



		return array(URLHelper::get_zframework_static_url('thirdparty/jquery-mobile/jquery.mobile-'.self::$_VERSION.'.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/jquery-mobile/jquery.mobile-'.self::$_VERSION.'.css'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERYUI);
	}
}