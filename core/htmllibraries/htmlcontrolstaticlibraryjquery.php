<?php 

class HTMLControlStaticLibraryJQuery extends HTMLControlStaticLibrary {
	
	public function get_library_js_files() {

		$jquery_files = array(URLHelper::get_zframework_static_url('thirdparty/jquery/jquery.min.js'));

//		if(ZPHP::is_mobile())
//		{
//			$jquery_mobile = self::_create_instance(self::STATIC_LIBRARY_JQUERY_MOBILE);
//			$jquery_files = array_merge($jquery_files, $jquery_mobile->get_library_js_files());
//		}

		return $jquery_files;
	}
	
	public function get_library_css_files() {

		$jquery_files = array();

//		if(ZPHP::is_mobile())
//		{
//			$jquery_mobile = self::_create_instance(self::STATIC_LIBRARY_JQUERY_MOBILE);
//			$jquery_files = array_merge($jquery_files, $jquery_mobile->get_library_css_files());
//		}

		return $jquery_files;

	}
}