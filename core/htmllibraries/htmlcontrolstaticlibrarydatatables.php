<?php 

class HTMLControlStaticLibraryDataTables extends HTMLControlStaticLibrary {
	

	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('js/zframework.php'),
			URLHelper::get_zframework_static_url('thirdparty/datatables/media/js/jquery.dataTables.js'),
			URLHelper::get_zframework_static_url('thirdparty/datatables/datatables-bootstrap/dataTables.bootstrap.js'),
			URLHelper::get_zframework_static_url('thirdparty/datatables/custom/custom.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/datatables/datatables-bootstrap/dataTables.bootstrap.css'),
			URLHelper::get_zframework_static_url('thirdparty/datatables/custom/custom.css'),
		);
	}

	public function get_dependence_libraries() {
		return array(
			self::STATIC_LIBRARY_JQUERY,
			self::STATIC_LIBRARY_BOOTSTRAP,
			self::STATIC_LIBRARY_FONTS_AWESOME,
		);
	}
}