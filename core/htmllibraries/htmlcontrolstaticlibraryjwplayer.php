<?php 

class HTMLControlStaticLibraryJWPlayer extends HTMLControlStaticLibrary {
	
	public function get_library_js_files() {

		$token = ZPHP::get_config('javascript_jwplayer_account_token');
		$url = "http://jwpsrv.com/library/{$token}.js";

		return array($url);
	}
	
	public function get_library_css_files() {
		return array();
	}
}