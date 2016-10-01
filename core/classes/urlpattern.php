<?php

class URLPattern {

	/**
	*
	* @var URLPattern[]
	*
	*/
	private static $_URLS = array();
	
	/* @return URLPattern */
	public static function get_url_pattern($id) {
		
		if(array_key_exists($id, self::$_URLS))
		{
			return self::$_URLS[$id];
		}
		else
		{
			foreach(self::$_URLS as $url_pattern)
			{
				if($url_pattern->get_redirect() == $id)
				{
					return $url_pattern;
				}
			}

			return null;
		}
		
	}
	
	public static function reverse($id, $arg1=null, $arg2=null) {
		
		$vars = array();
		$args = func_get_args();
		
		foreach($args as $index => $arg) {
			if($index == 0) continue;
			
			if(is_array($arg)) $vars = array_merge($vars, $arg);
			else $vars[] = $arg;
		}

		$url = self::get_url_pattern($id);
		
		if(!$url) return null;
		else return $url->format_url($vars);
	}


	public static function reverse_abs($id, $arg1=null, $arg2=null) {

		$args = func_get_args();
		$url = call_user_func_array(array(self, 'reverse'), $args);

		return NavigationHelper::conv_abs_url($url);

	}
	
	/*-----------------------------------------------------------------------------*/
	
	private static $_match_url_case_sensitive;
	private static $_match_url_strict_start;
	
	protected $_pattern;
	protected $_redirect;
	protected $_id;

	public function __construct($pattern, $id=null, $redirect=null) {
		
		$this->set_pattern($pattern);
		$this->set_redirect($redirect);
		$this->set_id($id ? $id : uniqid('url'));
	}

	public function __toString()
	{
		return $this->_redirect;
	}
	
	public function get_id() {
		return $this->_id;
	}

	public function set_id($value) {
		
		if($this->_id && array_key_exists($this->_id, self::$_URLS)) {
			unset(self::$_URLS[$this->_id]);
		}
		
		$this->_id = $value;
		
		if($this->_id) {
			self::$_URLS[$this->_id] = $this;
		}
		
		return $this;
	}

	
	public function get_pattern() {
		return $this->_pattern;
	}

	public function set_pattern($value) {
		$this->_pattern = $value;
		return $this;
	}

	public function get_redirect() {
		return $this->_redirect;
	}

	public function set_redirect($value) {
		$this->_redirect = (string) $value;
		return $this;
	}

	public function match_url($url) {

		if(is_null(self::$_match_url_case_sensitive))
			self::$_match_url_case_sensitive = ZPHP::get_config('redirect_control_url_pattern_case_sensitive');
		
		if(is_null(self::$_match_url_strict_start))
			self::$_match_url_strict_start = ZPHP::get_config('redirect_control_url_pattern_strict_start');

		$pattern = $this->get_pattern();

		$pattern = StringHelper::remove_prefix($pattern, '/');
		$pattern = "\/?{$pattern}";
					
		if(!self::$_match_url_case_sensitive) $pattern = "(?i){$pattern}";

		if(self::$_match_url_strict_start) $pattern = "^{$pattern}";
		
		$pattern = "{$pattern}\/?(?:\?)?(?:\?.+?)?(?:\#.+?)?$";

		if(strpos($pattern, '@') !== false) {
			$pattern = '@'.$pattern.'@';
		} else {
			$pattern = '#'.$pattern.'#';
		}

		if(preg_match($pattern, $url, $match)) {
			return $match;
		} else {
			return false;
		}
		
	}
	
	
	private static $_format_prepare_replaces = array(
		array('search' => '\\\\', 'escape' => '@@@BACKSLASH@@@', 'replace' => '\\'),
		array('search' => '\\(', 'escape' => '@@@POPEN@@@', 'replace' => '('),
		array('search' => '\\)', 'escape' => '@@@PCLOSE@@@', 'replace' => ')'),
	);
	
	private static $_format_group_prepare_replace = '@@@@GROUP@@@@';
	
	public function format_url($arg1=null, $arg2=null) {

		$url = $this->get_pattern();
		$vars = array();
		
		$args = func_get_args();
		
		foreach($args as $arg) {
			if(is_array($arg)) $vars = array_merge($vars, $arg);
			else $vars[] = $arg;
		}

		$url = StringHelper::remove_prefix($url, '\/?');
		$url = StringHelper::remove_prefix($url, '/?');
		$url = StringHelper::remove_prefix($url, '\/');
		$url = StringHelper::remove_prefix($url, '/');
		$url = "/".$url;

		$url = preg_replace('#(?i)\(([^\?].*?)\)\?#', '(?:${1})?', $url);
		$url = preg_replace('#\(\?\:.+?\)(?:\?|\+\??|\*\??|(?:\[.*?\]))?#', '', $url);
		$url = preg_replace('#\(\?[a-zA-Z]\)#', '', $url);				
		
		foreach(self::$_format_prepare_replaces as $format_prepare_replace)
			$url = str_replace($format_prepare_replace['search'], preg_quote($format_prepare_replace['escape']), $url);
		
		$url = str_replace('(?P<', '(', $url);
		$url = str_replace('\\', '', $url);
		$url = preg_replace('#(.)\?#', '', $url);

		while(preg_match('#(\(\?.+?\)\??)|(\(.+?\)\?)#', $url)) {
			$url = preg_replace('#(\(\?.+?\)\??)|(\(.+?\)\?)#', '', $url);
		}
		
		$url = preg_replace('#\(.+?\)#', self::$_format_group_prepare_replace, $url);
		
		while(strpos($url, self::$_format_group_prepare_replace) !== false) {
			
			if(!empty($vars)) {
				$var = array_shift($vars);
			} else {
				$var = '';
			}
			
			$url = preg_replace('#'.preg_quote(self::$_format_group_prepare_replace).'#', $var, $url, 1);
		}
		
		foreach(self::$_format_prepare_replaces as $format_prepare_replace)
			$url = str_replace(preg_quote($format_prepare_replace['escape']), $format_prepare_replace['replace'], $url);
		
		return ZPHP::get_config('site_url').$url;
		
	}
}