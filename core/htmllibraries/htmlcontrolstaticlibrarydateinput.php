<?php 

class HTMLControlStaticLibraryDateInput extends HTMLControlStaticLibrary {
	
	public function get_dependence_libraries() {
		return array(HTMLControlStaticLibraryModalDialog);
	}
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/dateinput/dateinput.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('js/plugins/dateinput/dateinput.css'));
	}

}