<?php 

class HTMLControlStaticLibraryAjaxForm extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/jquery-ajaxform/ajaxform.js'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERY);
	}
}