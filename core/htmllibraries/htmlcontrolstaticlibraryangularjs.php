<?php 

class HTMLControlStaticLibraryAngularJS extends HTMLControlStaticLibrary {
	
	
	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/angularjs/angular.js'),
			URLHelper::get_zframework_static_url('thirdparty/angularjs/angular-ui-router.min.js'),
			URLHelper::get_zframework_static_url('thirdparty/angularjs/angular-route.min.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
		);
	}
}