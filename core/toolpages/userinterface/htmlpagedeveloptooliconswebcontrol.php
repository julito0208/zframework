<?php 

class HTMLPageDevelopToolIconsWebControl extends HTMLPageDevelopToolIcons  {

	const URL_SCRIPT_PATTERN = '/icons\/web\-?control(?:\.php)?';
	const THEME = 'webcontrol';
	
	const DEFAULT_COLOR = 'black';
	const COLOR_GET_VARNAME = 'color';
	
	protected static $_sizes = array(32, 24, 16);
	protected static $_colors = array('black', 'blue', 'green', 'red', 'yellow');

	/*----------------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons Web Control';
	}
	
	protected static function _get_show_index()
	{
		return false;
	}
	
	protected $_selected_color;
	
	public function __construct() {
		
		parent::__construct(self::THEME);
		
		$this->_selected_color = $_GET[self::COLOR_GET_VARNAME];
		
		if(!$this->_selected_color || !in_array($this->_selected_color, self::$_colors)) {
			$this->_selected_color = self::DEFAULT_COLOR;
		}
		
		$this->_right_title_html = '';
		
		foreach(self::$_colors as $color) {
			
			$this->_right_title_html.= "<span style='margin: 2px 0 0 20px; line-height: 25px'>";
			
			if($color == $this->_selected_color) {
				$this->_right_title_html.= "<span style='color: #777; font-weight: bold'>{$color}</span>";
			} else {
				$href = NavigationHelper::make_url_query(array(self::COLOR_GET_VARNAME => $color));
				$this->_right_title_html.= "<a href='{$href}' class='color-link'>{$color}</a>";
			}
			
			$this->_right_title_html.= "</span>";
		}
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('sizes', self::$_sizes);
		$this->set_param('colors', self::$_colors);
		$this->set_param('selected_color', $this->_selected_color);
		
	}
}
