<?php 

abstract class HTMLControlStaticLibrary implements HTMLControlLibraryInterface{

	/**
	*
	* @return HTMLControlStaticLibrary
	*
	*/
	protected static function _create_instance($library)
	{
		return ClassHelper::create_instance($library);
	}

	/*-------------------------------------------------------------*/


	public function get_dependence_libraries() {
		return array();
	}
	
	public function get_library_js_files() {
		return array();
	}
	
	public function get_library_js_end_files() {
		return array();
	}

	public function get_library_js_ajax_files() {
		return array();
	}

	public function get_library_css_files() {
		return array();
	}
	
	public function update_html_control(HTMLControl $control) {}
	
	public function update_html_page(HTMLPage $page) {}
}
