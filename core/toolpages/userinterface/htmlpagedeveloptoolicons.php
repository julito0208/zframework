<?php 

class HTMLPageDevelopToolIcons extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/icons(?:\.php)?';

	/*----------------------------------------------*/
	
	protected static function _get_theme_file($theme) {
		$theme = str_replace(array('..', '/'), '', $theme);
		return ZPHP::get_zframework_dir()."/static/css/icons/{$theme}/names.txt";
	}
	
	protected static function _get_theme_array($theme) {
		$file = self::_get_theme_file($theme);
		@ $names = FilesHelper::file_read_array($file);
		return $names;		
	}
	
	/*----------------------------------------------*/

	
	protected static $_icons_pages_controls = array(
		'HTMLPageDevelopToolIconsFlags',
		'HTMLPageDevelopToolIconsGlyphicons',
		'HTMLPageDevelopToolIconsFontsAwesome',
		'HTMLPageDevelopToolIconsSilk',
		'HTMLPageDevelopToolIconsSimple', 
		'HTMLPageDevelopToolIconsSocialNetworks', 
		'HTMLPageDevelopToolIconsSweetieLegacy', 
		'HTMLPageDevelopToolIconsTango', 
		'HTMLPageDevelopToolIconsWebControl', 
		'HTMLPageDevelopToolIconsWebIcons',
	);
	
	public static function get_theme_pages_controls() {
		return self::$_icons_pages_controls;
	}
	
	/*-----------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Icons';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	/*----------------------------------------------*/
		
	protected $_selected_theme;
	protected $_names = array();
	protected $_themes = array();
	protected $_right_title_html = '';
	protected $_unselected_icon_label;
	protected $_show_icon_block = false;
	protected $_show_icon_block_button = false;

	public function __construct($theme=null) {
		
		parent::__construct();

		$this->_selected_theme = $theme;

		if(HTTPGet::exists('block'))
		{
			$this->_show_icon_block =HTTPGet::get_item_bool('block');
		}

		if($this->_selected_theme) {
			$this->_names = self::_get_theme_array($this->_selected_theme);
		}
		
		$this->_themes = array();
		
		foreach(self::get_theme_pages_controls() as $classname) {
			
			$this->_themes[] = array(
				'theme' => ClassHelper::get_class_constant($classname, 'THEME'),
				'control' => $classname,
				'url' => self::_get_zdevpage_urlpattern($classname)->format_url(),
			);
		}
		
		$this->_unselected_icon_label = "Pase el mouse por un icono para ver su clase";
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('selected_theme', $this->_selected_theme);
		$this->set_param('names', $this->_names);
		$this->set_param('themes', $this->_themes);
		$this->set_param('right_title_html', $this->_right_title_html);
		$this->set_param('unselected_icon_label', $this->_unselected_icon_label);
		$this->set_param('show_icon_block', $this->_show_icon_block);
		$this->set_param('show_icon_block_button', $this->_show_icon_block_button);
	}
}
