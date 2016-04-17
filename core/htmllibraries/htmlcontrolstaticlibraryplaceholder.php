<?php 

class HTMLControlStaticLibraryPlaceHolder extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/placeholder/placeholder.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/placeholder/placeholder.css'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT);
	}
}