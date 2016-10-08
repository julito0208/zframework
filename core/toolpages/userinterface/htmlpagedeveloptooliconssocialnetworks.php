<?php 

class HTMLPageDevelopToolIconsSocialNetworks extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/social-networks(?:\.php)?';
	const THEME = 'social-networks';
	
	protected static $_sizes = array(24, 16);

	/*----------------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons Social Network';
	}
	
	protected static function _get_show_index()
	{
		return false;
	}
	/*----------------------------------------------*/
		
	public function __construct() {
		parent::__construct(self::THEME);
		$this->add_css_files('/zframework/static/css/icons/social-networks/style.css');
	}
	
	public function prepare_params() {
		parent::prepare_params();
		$this->set_param('sizes', self::$_sizes);
	}
}
