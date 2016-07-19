<?php 


class HTMLPage extends HTMLPageBlank {

	public function __construct()
	{
		parent::__construct();
		self::add_global_static_library(self::STATIC_LIBRARY_ZSCRIPT);
		self::add_global_css_files_zframework('css/default.css');
		self::add_global_js_files_zframework('js/zframework.php');

	}

	public function get_icon() {
		return $this->_get_icon();
	}

	public function set_icon($value) {
		$this->_set_icon($value);
	}

	public function add_rss_files($file) {
		$args = func_get_args();
		foreach($args as $arg) {
			$this->_add_rss_files($arg);
		}
	}

	public function clear_rss_files() {
		$this->_clear_rss_files();
	}

	public function set_rss_files($file) {
		$this->clear_rss_files();
		$args = func_get_args();
		foreach($args as $arg) {
			$this->_add_rss_files($arg);
		}
	}

	
	public function get_rss_files() {
		return $this->_get_rss_files();
	}

	public function add_keywords($keywords) {
		$args = func_get_args();
		foreach($args as $arg) {
			$this->_add_keywords($arg);
		}	
	}


	public function clear_keywords() {
		$this->_clear_keywords();
	}
	
	public function set_keywords($keywords) {
		$this->clear_keywords();
		$args = func_get_args();
		foreach($args as $arg) {
			$this->_add_keywords($arg);
		}	
	}


	public function get_keywords() {
		return $this->_get_keywords();
	}

	public function get_ogp_enabled() {
		return $this->_ogp_enabled;
	}

	public function set_ogp_enabled($value) {
		$this->_ogp_enabled = $value;
	}

	public function get_ogp_description() {
		return $this->_ogp_description;
	}

	public function set_ogp_description($value) {
		$this->_ogp_description = $value;
	}

	public function get_ogp_title() {
		return $this->_ogp_title;
	}

	public function set_ogp_title($value) {
		$this->_ogp_title = $value;
	}

	public function get_ogp_url() {
		return $this->_ogp_url;
	}

	public function set_ogp_url($value) {
		$this->_ogp_url = $value;
	}

	public function get_ogp_image() {
		return $this->_ogp_image;
	}

	public function set_ogp_image($value) {
		$this->_ogp_image = $value;
	}

	public function get_ogp_type() {
		return $this->_ogp_type;
	}

	public function set_ogp_type($value) {
		$this->_ogp_type = $value;
	}

	public function add_meta_tag(array $attrs) {
		$this->_meta_tags[] = $attrs;
	}

	public function get_description() {
		return $this->_description;
	}

	public function set_description($value) {
		$this->_description = $value;
	}

	public function get_author() {
		return $this->_author;
	}

	public function set_author($value) {
		$this->_author = $value;
	}

	
}