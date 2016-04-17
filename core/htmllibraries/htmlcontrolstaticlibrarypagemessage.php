<?php 

class HTMLControlStaticLibraryPageMessage extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/pagemessage/pagemessage.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/pagemessage/pagemessage.css'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_FIXED_STATIC_BLOCK);
	}
}