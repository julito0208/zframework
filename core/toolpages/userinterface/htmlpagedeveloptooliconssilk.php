<?php 

class HTMLPageDevelopToolIconsSilk extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/silk(?:\.php)?';
	const THEME = 'silk';

	
	protected static function _get_title()
	{
		return 'Icons Silk';
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
