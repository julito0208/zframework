<?php 

class HTMLPageDevelopToolIconsFlags extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/flags(?:\.php)?';
	const THEME = 'flags';

	/*----------------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons Flags';
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
		
	}
}
