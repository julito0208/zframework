<?php 

class HTMLPageDevelopToolIconsTango extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/tango(?:\.php)?';
	const THEME = 'tango';

	protected static $_sizes = array(32, 24, 16);
	
	/*----------------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons Tango';
	}
	
	protected static function _get_show_index()
	{
		return false;
	}
	
	/*----------------------------------------------*/
		
	public function __construct() {
		parent::__construct(self::THEME);
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('sizes', self::$_sizes);
	}
}
