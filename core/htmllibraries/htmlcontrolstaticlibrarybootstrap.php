<?php

class HTMLControlStaticLibraryBootStrap extends HTMLControlStaticLibrary {
	
	public function get_library_js_end_files() {
		return array(URLHelper::get_zframework_static_url('thirdparty/bootstrap/js/bootstrap.js'));
	}
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/bootstrap/css/bootstrap.css (media:screen)'),
			URLHelper::get_zframework_static_url('thirdparty/bootstrap/css/bootstrap-theme.css'),
		);
	}
	
	public function update_html_page(HTMLPage $page) {
		$page->add_meta_tag(array('http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge'));
		$page->add_meta_tag(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'));
	}

	public function get_dependence_libraries() {
		return array(self::STATIC_LIBRARY_JQUERY);
	}
}
