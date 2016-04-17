<?php 

class HTMLControlStaticLibraryHTMLBox extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/htmlbox/xhtml.js'),
			URLHelper::get_zframework_static_url('thirdparty/htmlbox/htmlbox.styles.js'),
			URLHelper::get_zframework_static_url('thirdparty/htmlbox/htmlbox.colors.js'),
			URLHelper::get_zframework_static_url('thirdparty/htmlbox/htmlbox.full.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/htmlbox/htmlbox.css'),
		);
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERY);
	}
}