<?php 

class HTMLControlStaticLibraryListMenu extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/listmenu/listmenu.js'));
	}
	
	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT);
	}
}