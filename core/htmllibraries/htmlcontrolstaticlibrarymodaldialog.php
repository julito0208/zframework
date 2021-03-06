<?php 

class HTMLControlStaticLibraryModalDialog extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/modaldialog/modaldialog.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/modaldialog/modaldialog.css'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT, self::STATIC_LIBRARY_BOOTSTRAP, self::STATIC_LIBRARY_MASONRY, self::STATIC_LIBRARY_FONTS_AWESOME);
	}
}