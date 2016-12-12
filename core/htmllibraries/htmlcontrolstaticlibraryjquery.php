<?php 

class HTMLControlStaticLibraryJQuery extends HTMLControlStaticLibrary {
	
	public function get_library_js_files() {

		$jquery_files = array(URLHelper::get_zframework_static_url('thirdparty/jquery/jquery.min.js'));

		return $jquery_files;
	}
	
	public function get_library_css_files() {

		$jquery_files = array();

		return $jquery_files;

	}
}