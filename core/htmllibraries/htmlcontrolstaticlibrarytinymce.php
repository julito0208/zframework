<?php 

class HTMLControlStaticLibraryTinyMCE extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/tinymce/js/tinymce/tinymce.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/tinymce/js/tinymce/tinymce.css'),
		);
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT);
	}
}