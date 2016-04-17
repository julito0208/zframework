<?php 

class HTMLControlStaticLibraryNumericField extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/numericfield/numericfield.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/numericfield/numericfield.css'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT);
	}
}