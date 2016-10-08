<?php 

class HTMLPageDevelopToolIconsSweetieLegacy extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/sweetie\-?legacy(?:\.php)?';
	const THEME = 'sweetie-legacy';

	protected static $_sizes = array(24, 16, 12, 8);
	
	/*----------------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons Sweetie Legacy';
	}
	
	protected static function _get_show_index()
	{
		return false;
	}
	
	/*----------------------------------------------*/
		
	public function __construct() {
		parent::__construct(self::THEME);
		$this->add_css_files('/zframework/static/css/icons/sweetie-legacy/style.css');
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('sizes', self::$_sizes);
		
	}
}
