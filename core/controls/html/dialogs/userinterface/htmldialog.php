<?php 

class HTMLDialog extends AjaxHTMLResponse {

	const MODALDIALOG_MODE_APPEND = 1;
	const MODALDIALOG_MODE_REPLACE = 2;
	const MODALDIALOG_MODE_REPLACE_ALL = 3;

	protected $_modaldialog_js_files = array();
	protected $_modaldialog_css_files = array();
	protected $_modaldialog_out_json = true;
	protected $_dialog_options = array();
	
	public function __construct() {
		
		parent::__construct();
		LanguageHelper::set_current_language_from_url($_SERVER['HTTP_REFERER']);
	}
	
	public function get_modaldialog_out_json() {
		return $this->_modaldialog_out_json;
	}

	public function set_modaldialog_out_json($value) {
		$this->_modaldialog_out_json = $value;
	}
	
	protected function _prepare_js_files_html(array $js_files) {
		
		if(!$this->_modaldialog_out_json) {
			return parent::_prepare_js_files_html($js_files);
		} else {
			$this->_modaldialog_js_files = $js_files;
			return '';
		}
	}
	
	protected function _prepare_css_files_html(array $css_files) {
		
		if(!$this->_modaldialog_out_json) {
			return parent::_prepare_css_files_html($css_files);
		} else {
			$this->_modaldialog_css_files = $css_files;
			return '';
		}
	}

	public function update_dialog_options(array $params = array())
	{
		$this->_dialog_options = array_merge($this->_dialog_options, $params);
		return $this;
	}

	public function set_dialog_option($name, $value=null)
	{
		if(is_null($value))
		{
			unset($this->_dialog_options[$name]);
		}
		else
		{
			$this->_dialog_options[$name] = $value;
		}

		return $this;
	}

	public function get_dialog_option($name)
	{
		return $this->_dialog_options[$name];
	}

	public function clear_dialog_option($name)
	{
		$args = func_get_args();
		foreach($args as $arg)
		{
			$this->set_dialog_option($arg, null);
		}
		return $this;
	}

	public function clear_dialog_options()
	{
		$this->_dialog_options = array();
		return $this;
	}


	public function out() {

		if(!$this->_modaldialog_out_json) {
			
			parent::out();
			
		} else {

			$json = new JSONMap();

			$json->set_item('options', $this->_dialog_options);
			$json->set_item('html', $this->to_string());
			$json->set_item('js_files', $this->_modaldialog_js_files);
			$json->set_item('css_files', $this->_modaldialog_css_files);
			
			$json->out();
			
		}
		
	}
	
	
}