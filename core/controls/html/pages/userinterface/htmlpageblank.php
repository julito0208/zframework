<?php 


class HTMLPageBlank extends HTMLControl {

	protected static $_default_ogp_enabled = true;
	protected static $_default_ogp_type = 'website';

	protected static $_session_page_uris_historial_varname = 'pages_uris';

	//------------------------------------------------------------------------------------

	public static function get_permissions()
	{
		return array();
	}

	public static function get_page_last_url($page=null, $vars=null)
	{
		if(func_num_args() == 0)
		{
			$page = get_class();
		}

		SessionHelper::init();

		$historial = SessionHelper::get_var(self::$_session_page_uris_historial_varname);

		if(!$historial)
		{
			$historial = array();
		}

		if(array_key_exists($page, $historial))
		{
			$url = $historial[$page];
		}
		else
		{
			$url = URLPattern::reverse($page);
		}

		return NavigationHelper::make_url_query($vars, $url);
	}

	protected static function _get_resource_link($url)
	{
		if(preg_match('#(\w+\:\/\/.+)#', $url, $match))
		{
			return $match[0];
		}
		else
		{
			return $url;
		}
	}
	
	//------------------------------------------------------------------------------------
	
	private $_title;
	private $_base_url;
	private $_language;
	private $_icon;
	private $_rss_files = array();
	protected $_keywords = array();
	protected $_description;
	protected $_author;
	protected $_ogp_enabled;
	protected $_ogp_title;
	protected $_ogp_description;
	protected $_ogp_image;
	protected $_ogp_url;
	protected $_ogp_type;
	protected $_meta_tags = array();
	protected $_use_debug_bar = false;
	
	public function __construct($params=null) {
		parent::__construct($params);

		$this->set_title(ZPHP::get_config('html_title'));
		$this->set_language(ZPHP::get_config('html_language'));
		$this->set_base_url(ZPHP::get_config('html_base_url'));
		
		$this->_add_rss_files(ZPHP::get_config('html_rss_files'));
		$this->_add_keywords(ZPHP::get_config('html_keywords'));
		$this->_set_icon(ZPHP::get_config('html_icon'));
		
		$this->_ogp_enabled = self::$_default_ogp_enabled;
		$this->_ogp_type = self::$_default_ogp_type;
		
		$this->_description = ZPHP::get_config('html.description');
		$this->_author = ZPHP::get_config('html.author');

		$this->add_css_files(ZPHP::get_config('html_css_files'));
		$this->add_js_files(ZPHP::get_config('html_js_files'));
		
		$this->_set_parse_all_parents_templates(true);

		$this->_update_page_uri_historial();

		$this->_use_debug_bar = ZPHP::is_debug_mode();
		$this->_include_global_static = true;
	}

	/*-------------------------------------------------------------*/

	protected function _update_page_uri_historial()
	{
		SessionHelper::init();

		$historial = SessionHelper::get_var(self::$_session_page_uris_historial_varname);

		if(!$historial)
		{
			$historial = array();
		}

		$historial[get_class($this)] = ZPHP::get_absolute_actual_uri();

		SessionHelper::add_var(self::$_session_page_uris_historial_varname, $historial);

	}
	
	//------------------------------------------------------------------------------------
	
	
	protected function _prepare_parsed_content($content) {

		$first_js_files = array_merge($this->_js_files, array());
		$first_css_files = array_merge($this->_css_files, array());
		
		if($this->_is_main_parsing_control) {
			$first_js_files = array_merge($first_js_files, self::$_global_js_files);
			$first_css_files = array_merge($first_css_files, self::$_global_css_files);
		}

		$first_js_files = array_unique(self::_prepare_js_files_array(array_filter(array_unique($first_js_files))));
		$first_css_files = self::_prepare_css_files_array(self::_css_files_unique($first_css_files));

		$end_js_files = array_merge($this->_end_js_files, array());
		
		if($this->_is_main_parsing_control) {
			$end_js_files = array_merge($end_js_files, self::$_global_end_js_files);
		}

		$end_js_files = self::_prepare_js_files_array(array_filter(array_unique($end_js_files)));

		$ajax_js_files = array_merge(self::$_global_ajax_js_files, array());
		$ajax_js_files = self::_prepare_js_files_array(array_filter(array_unique($ajax_js_files)));

		if($this->_is_main_parsing_control) {
			self::$_global_js_files = array();
			self::$_global_css_files = array();
		}
		
		$this->set_param('language', $this->_language);
		$this->set_param('charset', $this->_charset);
		$this->set_param('keywords', $this->_keywords);
		$this->set_param('ogp_enabled', $this->_ogp_enabled);
		$this->set_param('ogp_title', $this->_ogp_title);
		$this->set_param('ogp_description', $this->_ogp_description);
		$this->set_param('ogp_image', $this->_ogp_image);
		$this->set_param('ogp_url', $this->_ogp_url);
		$this->set_param('ogp_type', $this->_ogp_type);
		$this->set_param('title', $this->_title);
		$this->set_param('meta_tags', $this->_meta_tags);
		$this->set_param('title', $this->_title);
		$this->set_param('base_url', $this->_base_url);
		$this->set_param('icon', $this->_icon);
		$this->set_param('rss_files', $this->_rss_files);
		$this->set_param('first_css_files', $first_css_files);
		$this->set_param('first_js_files', $first_js_files);
		$this->set_param('end_js_files', $end_js_files);
		$this->set_param('author', $this->_author);
		$this->set_param('description', $this->_description);

		
		$html = "<!DOCTYPE html>\n";
		$html.= "<html lang='".HTMLHelper::escape($this->_language)."'>\n";
		$html.= "<head>\n";
		
		if($this->_charset) {
			$html.= "<meta charset='".HTMLHelper::escape($this->_charset)."' />\n";
		}
		
		if(!empty($this->_keywords)) {
			$html.= "<meta name='keywords' content='".HTMLHelper::escape(implode(',', $this->_keywords))."' />\n";
		}
		
		if($this->_description) {
			$html.= "<meta name='description' content='".HTMLHelper::escape($this->_description)."' />\n";
		}
		
		if($this->_author) {
			$html.= "<meta name='author' content='".HTMLHelper::escape($this->_author)."' />\n";
		}

		if($this->_ogp_enabled) {
			
			$ogp_title = $this->_ogp_title ? $this->_ogp_title : $this->_title;
//			$ogp_url = $this->_ogp_url ? $this->_ogp_url : ZPHP::get_config('site_url').$_SERVER['REQUEST_URI'].'?'.http_build_query($_GET);
			$ogp_url = $this->_ogp_url ? $this->_ogp_url : ZPHP::get_absolute_actual_uri();

			$html.= "<meta property='og:url' content='".HTMLHelper::escape($ogp_url)."' />\n";
			$html.= "<meta property='og:type' content='".HTMLHelper::escape($this->_ogp_type)."' />\n";
			
			if($this->_ogp_title) {
				$html.= "<meta property='og:title' content='".HTMLHelper::escape($ogp_title)."' />\n";
			}
			
			if($this->_ogp_description || $this->_description) {
				$html.= "<meta property='og:description' content='".HTMLHelper::escape($this->_ogp_description ? $this->_ogp_description : $this->_description)."' />\n";
			}
			
			if($this->_ogp_image) {
				$html.= "<meta property='og:image' content='".HTMLHelper::escape(NavigationHelper::conv_abs_url($this->_ogp_image))."' />\n";
			}
		}
		
		
		foreach($this->_meta_tags as $meta_tag) {
			
			$meta_str = '';
			
			foreach((array) $meta_tag as $key => $value) {
				$meta_str.= " {$key}=".HTMLHelper::quote($value);
			}
			
			$html.= "<meta {$meta_str} />\n";
		}
		
		if($this->_title) {
			$html.= "<title>".HTMLHelper::escape($this->_title)."</title>\n";
		}
		
		if($this->_base_url) {
			$html.= "<base href='".HTMLHelper::escape($this->_base_url)."' />\n";
		}
		
		if($this->_icon) {
			$html.= "<link href='".HTMLHelper::escape(self::_convert_resource_url($this->_icon))."' type='".HTMLHelper::escape(FilesHelper::file_get_mimetype($this->_icon))."' rel='shortcut icon' />\n";
		}

		if(!empty($first_js_files)) {

			if(false && ZPHP::get_config('html.unify_js.enabled'))
			{
				$js_unify_files = [];
				$js_php_files = [];

				foreach($first_js_files as $js_file)
				{
					if(preg_match('#(?i)\.php$#', $js_file))
					{
						$js_php_files[] = $js_file;
					}
					else
					{
						$js_unify_files[] = $js_file;
					}
				}

				$unify_dir = ZPHP::get_www_dir().ZPHP::get_config('html.unify_js.dir');
				FilesHelper::path_make_dir($unify_dir);

				$dest_basename = md5(implode('', $js_unify_files)).'.js';
				$dest_path = $unify_dir.'/'.$dest_basename;

				if(!file_exists($dest_path))
				{
					$parsed_js_files = [];

					foreach ($js_unify_files as $js_file)
					{

						$js_file = self::_convert_resource_url($js_file);

						$js_file_wp = preg_replace('#^\w+\:\/\/'.preg_quote(ZPHP::get_config('site_domain').ZPHP::get_config('site_document_path')).'#', '', $js_file);

						if($js_file_wp != $js_file)
						{
							if(StringHelper::starts_with($js_file_wp, ZPHP::get_config('zframework_static.url')))
							{
								$read_file = ZPHP::get_zframework_dir().StringHelper::remove_prefix($js_file_wp, '/zframework');
							}
							else
							{
								$read_file = $js_file_wp;
							}

						}
						else
						{
							$read_file = $js_file;
						}

						$parsed_js_files[] = $read_file;

					}

					foreach($parsed_js_files as $js_file)
					{
						$js_file_contents = file_get_contents($js_file);
						file_put_contents($dest_path, $js_file_contents."\n", FILE_APPEND);
					}

				}

				$used_js_files = [];

				foreach($js_php_files as $js_file) {

					$js_file = self::_convert_resource_url($js_file);

					if(!in_array($js_file, $used_js_files))
					{
						$used_js_files[] = $js_file;

//						if(StringHelper::ends_with($js_file, 'zframework.php'))
//						{
//							$js_file.= ".aaaa";
//						}

						$html.= "<script type='text/javascript' src='".HTMLHelper::escape($js_file)."'></script>\n";
					}

				}

				$unify_url = ZPHP::get_config('html.unify_js.dir').'/'.$dest_basename;
				$unify_url = NavigationHelper::conv_abs_url($unify_url);
				$html.= "<script type='text/javascript' src='".HTMLHelper::escape($unify_url)."'></script>\n";

			}
			else
			{
				$used_js_files = [];

				foreach($first_js_files as $js_file) {

//					echo ($js_file)."\n";
//					if(preg_match('#(?i)^\w+\:\/\/.*#', $js_file))
//					{
//
//						if(basename($js_file) == 'gmapcontrol.js')
//						{
//							die($js_file);
//						}
//						$js_file = $js_file;
//					}
//					else
//					{
//						$js_file = self::_convert_resource_url($js_file);
//					}

					$js_file = self::_get_resource_link($js_file);
					$zframework_used = false;

					if(!in_array($js_file, $used_js_files))
					{
						$used_js_files[] = $js_file;

						if(StringHelper::ends_with($js_file, 'zframework.php'))
						{
							if($zframework_used)
							{
								continue;
							}

							$js_file.= "?language=".LanguageHelper::get_current_language();
							$zframework_used = true;
						}

						$html.= "<script type='text/javascript' src='".HTMLHelper::escape($js_file)."'></script>\n";
					}

				}
			}

		}
		
		if(!empty($this->_rss_files)) {
			foreach($this->_rss_files as $rss_file) {
				$html.= "<link rel='alternate' type='application/rss+xml' href='".HTMLHelper::escape(self::_convert_resource_url($rss_file))."' />\n";
			}
		}
		
		if(!empty($first_css_files)) {
			foreach($first_css_files as $css_file) {
				$html.= "<link rel='stylesheet' type='text/css' href='".HTMLHelper::escape(self::_convert_resource_url(self::_get_resource_link($css_file['file'])))."' media='".HTMLHelper::escape($css_file['media'])."' />\n";
			}
		}
		
		$html.= "</head>\n<body>";
		
		$content = self::_prepare_html_content($content);

		if($this->_use_debug_bar)
		{
			$debug_data = ZPHP::get_debug_data();
			$debug_block = new HTMLBlockDebugData($debug_data, strlen($content));
			$content = $debug_block->to_string().$content;
		}

		$html.= self::_comment_js_code($content);
		
		if(!empty($end_js_files)) {
			foreach($end_js_files as $js_file) {
				$html.= "<script type='text/javascript' src='".HTMLHelper::escape(self::_convert_resource_url($js_file))."'></script>\n";
			}
		}

		if(!empty($ajax_js_files)) {
			foreach($ajax_js_files as $js_file) {
				$html.= "<script type='text/javascript'> \$(document).ready(function() { \$.getScript('".self::_convert_resource_url($js_file)."'); }); </script>\n";
			}
		}


		if($this->_title || $this->_description)
		{
			$html.= "\n<div style='display:none'>";
			
			if($this->_title)
			{
				$html.= "<h1>{$this->_title}</h1>";
			}
			
			if($this->_description)
			{
				$html.= "<h2>{$this->_description}</h2>";
			}
			
			$html.= "</div>";
		}
		
		$html.= "</body>\n</html>";
		
		return $html;

	}
	
	//------------------------------------------------------------------------------------

	protected function _get_icon() {
		return $this->_icon;
	}

	protected function _set_icon($value) {
		$this->_icon = $value;
	}
	
	
	protected function _add_rss_files($file) {
		foreach((array) $file as $rss_file) {
			if($rss_file) $this->_rss_files[] = $rss_file;
		}
	}

	protected function _clear_rss_files() {
		$this->_rss_files = array();
	}

	protected function _get_rss_files() {
		return array_merge($this->_rss_files, array());
	}
	
	
	protected function _add_keywords($keywords) {
		foreach((array) $keywords as $keyword) {
			if($keyword) $this->_keywords[] = $keyword;
		}	
	}


	protected function _clear_keywords() {
		$this->_keywords = array();
	}
	
	protected function _get_keywords() {
		return array_merge($this->_keywords, array());
	}
	//------------------------------------------------------------------------------------
	
	public function get_title() {
		return $this->_title;
	}

	public function set_title($value) {
		$this->_title = $value;
	}

	public function get_base_url() {
		return $this->_base_url;
	}

	public function set_base_url($value) {
		$this->_base_url = $value;
	}
	
	public function get_language() {
		return $this->_language;
	}

	public function set_language($value) {
		$this->_language = $value;
	}

}