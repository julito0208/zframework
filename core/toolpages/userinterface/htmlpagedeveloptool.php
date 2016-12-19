<?php 

class HTMLPageDevelopTool extends HTMLPage  implements AccessControlDevelopment, RedirectURLPattern {
	
	protected static $_DEFAULT_URL_DIRNAME = 'ztools';
	
	/* @return URLPattern */
	protected static function _get_zdevpage_urlpattern($classname) {

		$url_id = $classname;
		
		$pattern = URLPattern::get_url_pattern($url_id);
		
		if(!$pattern) {
		
			$url_dirname = '/'.ZPHP::get_config('redirect_control_dev_tools_dirname', self::$_DEFAULT_URL_DIRNAME);

			$classname = (string) $classname;
			eval("\$url_pattern = {$classname}::URL_SCRIPT_PATTERN;");

			return new URLPattern('(?i)'.preg_quote($url_dirname).$url_pattern, $classname, $classname);
			
		} else {
			
			return $pattern;
		}
	}	
	
	public static function get_tools_urls() {
		
		$urls = array();
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolEntityGenerator');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolScriptsMin');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolIcons');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolLanguageTexts');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolMigrations');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolImages');
		$urls[] = self::_get_zdevpage_urlpattern('HTMLPageDevelopToolIndexPage');
		
		foreach(HTMLPageDevelopToolIcons::get_theme_pages_controls() as $classname) {
			$urls[] = self::_get_zdevpage_urlpattern($classname);
		}

		return $urls;
	}
	
	/*-----------------------------------------*/
	
	protected static function _get_title()
	{
		return '';
	}
	
	protected static function _get_show_index()
	{
		return false;
	}
	
	/*-----------------------------------------*/

	protected $_show_back = true;
	
	public function __construct() {
		parent::__construct();
		self::add_global_static_library(self::STATIC_LIBRARY_ZSCRIPT);
		self::add_global_static_library(self::STATIC_LIBRARY_MODAL_DIALOG);
		self::add_global_static_library(self::STATIC_LIBRARY_FONTS_AWESOME);
		$this->_use_debug_bar = false;
	}
	
	public function prepare_params()
	{
		$this->set_param('show_back', $this->_show_back);
		$this->set_param('ztools_url', ZPHP::get_config('redirect_control_dev_tools_dirname', self::$_DEFAULT_URL_DIRNAME));
		parent::prepare_params();
	}
}
