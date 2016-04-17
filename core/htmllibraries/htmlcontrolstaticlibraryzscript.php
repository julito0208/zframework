<?php 

class HTMLControlStaticLibraryZScript extends HTMLControlStaticLibrary {
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/zframework.php'), URLHelper::get_zframework_static_url('js/zscript.js'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERY);
	}


}