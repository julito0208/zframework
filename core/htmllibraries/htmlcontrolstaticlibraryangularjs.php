<?php 

class HTMLControlStaticLibraryAngularJS extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/angularjs/angular.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
		);
	}
}