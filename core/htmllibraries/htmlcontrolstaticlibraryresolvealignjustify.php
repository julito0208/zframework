<?php 

class HTMLControlStaticLibraryResolveAlignJustify extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/resolvealignjustify/resolvealignjustify.js'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_ZSCRIPT);
	}
}