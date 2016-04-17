<?php 

class HTMLControlStaticLibraryDataTablesTreeView extends HTMLControlStaticLibrary {
	

	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/datatables-treeview/jquery.treetable.js'),
		);
	}
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/datatables-treeview/css/jquery.treetable.css'),
			URLHelper::get_zframework_static_url('thirdparty/datatables-treeview/css/jquery.treetable.theme.default.css'),
		);
	}

	public function get_dependence_libraries() {
		return array(
			self::STATIC_LIBRARY_DATA_TABLES,
			self::STATIC_LIBRARY_JQUERYUI,
		);
	}
}