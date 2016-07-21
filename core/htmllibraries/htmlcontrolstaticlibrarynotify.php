<?php 

class HTMLControlStaticLibraryNotify extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/notify/notify.js'));
	}

	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/notify/notify.css'));
	}

	public function get_dependence_libraries() {
		return array(
			self::STATIC_LIBRARY_JQUERY,
			self::STATIC_LIBRARY_FONTS_AWESOME,
			self::STATIC_LIBRARY_BOOTSTRAP
		);
	}
}