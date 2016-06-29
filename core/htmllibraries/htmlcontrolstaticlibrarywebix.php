<?php

class HTMLControlStaticLibraryWebix extends HTMLControlStaticLibrary {
	
	public function get_library_js_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/webix/codebase/webix.js'));
	}
	
	public function get_library_css_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/webix/codebase/webix.css'));
	}
}
