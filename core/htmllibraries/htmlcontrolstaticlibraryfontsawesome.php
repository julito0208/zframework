<?php

class HTMLControlStaticLibraryFontsAwesome extends HTMLControlStaticLibrary {
	
	public function get_library_css_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/fonts-awesome/css/font-awesome.css'),
		);
	}

}
