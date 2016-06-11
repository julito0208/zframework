<?php

class RedirectControl {

	private static $_URLS = array();
	private static $_URLS_MOBILE = array();

	private static $_DEFAULT_REDIRECT_URL_PATTERN_CLASSES = array(
		'ImageCaptchaControl',
		'HTMLDialogCropImage',
		'HTMLDialogUploadFile',
		'FileImageThumbControl',
		'HTMLPageUserLogout',
		'MercadoPagoIpn',
		
	);

	private static $_URL_PATTERN_AJAX_STATIC_CALL_METHODS = null;
	private static $_URL_PATTERN_AJAX_HANDLERS_CALL = null;
	private static $_URL_PATTERN_ZFRAMEWORK_STATIC = null;

	private static $_ZFRAMEWORK_STATIC_CACHE_DEFAULT_ENABLED = 1;
	private static $_ZFRAMEWORK_STATIC_CACHE_DEFAULT_DAYS = 90;

	protected static $_IS_AJAX_CALL = false;

	/*--------------------------------------------------------------------------------*/

	protected static function _add_redirects(&$urls, $pattern=null, $redirect=null, $id=null) {

		$urls[] = new URLPattern($pattern, $id ? $id : $redirect, $redirect);

	}
	
	protected static function _add_redirects_urls($pattern=null, $redirect=null, $id=null) {

		self::_add_redirects(self::$_URLS, $pattern, $redirect, $id);

	}
	
	protected static function _add_redirects_mobile($pattern=null, $redirect=null, $id=null) {
		
		self::_add_redirects(self::$_URLS_MOBILE, $pattern, $redirect, $id);
		
	}
	
	protected static function _access_control($classname)
	{

		if(ClassHelper::class_method_exists($classname, 'get_permissions') && method_exists(array('User', 'logged_user_has_all_permissions')))
		{
			$permissions = call_user_func_array(array($classname, 'get_permissions'));

			if(!User::logged_user_has_all_permissions($permissions))
			{
				$html = new HTMLPageUnauthorized();
				$html->out();
				die();
			}

		}

		if(!ZPHP::get_config('access_control_enabled') || ZPHP::is_development_mode())
		{
			return true;
		}

		if(is_object($classname))
		{
			return self::_access_control(get_class($classname));
		}
		
		if(!is_subclass_of($classname, 'AccessControl'))
		{
			return true;
		}
		
		$users = array();

		if(is_subclass_of($classname, 'AccessControlDevelopment'))
		{
			$users[] = ZPHP::get_config('access_control_master');
			$users[] = ZPHP::get_config('access_control_development');
			$prompt = ZPHP::get_config('access_control_development_prompt_text');
		}
		else if(is_subclass_of($classname, 'AccessControlAdmin'))
		{
			if(ZPHP::get_config('access_control_admin')->user)
			{
				$users[] = ZPHP::get_config('access_control_master');
				$users[] = ZPHP::get_config('access_control_development');
				$users[] = ZPHP::get_config('access_control_admin');
				$prompt = ZPHP::get_config('access_control_admin_prompt_text');
			}
		}
		else if(is_subclass_of($classname, 'AccessControlPublic'))
		{
			if(ZPHP::get_config('access_control_public')->user)
			{
				$users[] = ZPHP::get_config('access_control_master');
				$users[] = ZPHP::get_config('access_control_development');
				$users[] = ZPHP::get_config('access_control_admin');
				$users[] = ZPHP::get_config('access_control_public');
				$prompt = ZPHP::get_config('access_control_public_prompt_text');
			}
		}
		else
		{
			if(ZPHP::get_config('access_control')->user)
			{
				$users[] = ZPHP::get_config('access_control_master');
				$users[] = ZPHP::get_config('access_control_development');
				$users[] = ZPHP::get_config('access_control_admin');
				$users[] = ZPHP::get_config('access_control_public');
				$users[] = ZPHP::get_config('access_control');
				$prompt = ZPHP::get_config('access_control_prompt_text');
			}
		}

		if($users)
		{
			$auth_users = array();
			
			foreach($users as $user)
			{
				if($user && $user->user)
				{
					$auth_users[] = array('user' => $user->user, 'password' => $user->password);
				}
			}

			if(NavigationHelper::test_http_basic_auth_users($auth_users))
			{
				return true;
			}
			else
			{
				if(NavigationHelper::require_http_basic_auth_users($auth_users, $prompt))
				{
					return true;
				}
				else
				{
					$html = new HTMLPageUnauthorized();
					$html->out();
					die();
				}
			}
		}
		else
		{
			return true;
		}
	}
	
	protected static function _test_redirect_urls($uri, $urls)
	{
		if(($match = self::$_URL_PATTERN_ZFRAMEWORK_STATIC->match_url($uri))) {

			$path = $match['path'];
			$fullpath = ZPHP::get_config('zframework_dir').'/static/'.ltrim($path, '/');

			$cache_enabled = ZPHP::get_config('zframework_static_cache_enabled', self::$_ZFRAMEWORK_STATIC_CACHE_DEFAULT_ENABLED);
			$cache_days = ZPHP::get_config('zframework_static_cache_days', self::$_ZFRAMEWORK_STATIC_CACHE_DEFAULT_DAYS);

			if($cache_enabled) {
				NavigationHelper::header_cache_enable($cache_days * 24 * 60);
			} else {
				NavigationHelper::header_cache_revalidate();
			}
 			
			if(preg_match('#(?i).*?\.php$#', $fullpath)) {
				@ include($fullpath);
				die();
			} else {
				NavigationHelper::content_file_out($fullpath);					
				die();
			}

		}

		foreach(array_reverse($urls) as $index => $url) {

			if(($match = $url->match_url($uri))) {

				$redirect = $url->get_redirect();
				$vars = array();

				foreach($match as $key => $value) {
					if(is_numeric($key) && $key != '0') {
						$vars[] = $value;
					} 
				}



				self::$_IS_AJAX_CALL = $obj && is_subclass_of($redirect, 'AjaxResponse');

				$obj = ClassHelper::create_instance_array($redirect, $vars);

				if($obj) {

					if($obj instanceof MIMEControl && self::_access_control($obj)) {
						$obj->out();
						die();
					}
				}

			}
		}
		
		self::$_IS_AJAX_CALL = false;
		
		return false;
		
	}
	
	protected static function _redirect_process_uri($uri) {

		self::$_IS_AJAX_CALL = false;
		
		$urls = array_merge(self::$_URLS, array());

		foreach(self::$_DEFAULT_REDIRECT_URL_PATTERN_CLASSES as $classname)
		{
			$pattern = call_user_func_array(array($classname, 'get_url_pattern'), array());

			if(is_array($pattern))
			{
				$urls = array_merge($urls, $pattern);
			}
			else
			{
				$urls[] = $pattern;
			}
		}

		$urls = array_merge($urls, HTMLPageDevelopTool::get_tools_urls());
		return self::_test_redirect_urls($uri, $urls);
	}
	
	protected static function _redirect_process_uri_mobile($uri) {
		
		self::$_IS_AJAX_CALL = false; 
		
		$urls = array_merge(self::$_URLS_MOBILE, array());

		foreach(self::$_DEFAULT_REDIRECT_URL_PATTERN_CLASSES as $classname)
		{
			$pattern = call_user_func_array(array($classname, 'get_url_pattern'), array());

			if(is_array($pattern))
			{
				$urls = array_merge($urls, $pattern);
			}
			else
			{
				$urls[] = $pattern;
			}
		}

		return self::_test_redirect_urls($uri, $urls);
	}

	
	protected static function _redirect_process() {

		try 
		{
					
			if(is_null(self::$_URL_PATTERN_AJAX_STATIC_CALL_METHODS)) {
				self::$_URL_PATTERN_AJAX_STATIC_CALL_METHODS = AjaxHandler::get_static_method_url_pattern();
			}

			if(is_null(self::$_URL_PATTERN_AJAX_HANDLERS_CALL)) {
				self::$_URL_PATTERN_AJAX_HANDLERS_CALL = AjaxHandler::get_handler_url_pattern();
			}

			if(is_null(self::$_URL_PATTERN_ZFRAMEWORK_STATIC)) {
				self::$_URL_PATTERN_ZFRAMEWORK_STATIC = new URLPattern(preg_quote(rtrim(ZPHP::get_config('zframework_static_url'), '/')).'/(?P<path>.+)');
			}

			$uri_complete = ZPHP::get_actual_uri();

			if(strpos($uri_complete, '?') !== false) {
				list($uri, $query_string) = explode('?', $uri_complete);
			} else {
				$uri = $uri_complete;
				$query_string = '';
			}


			$uri = str_replace('..', '', $uri);

			if(($match = self::$_URL_PATTERN_AJAX_STATIC_CALL_METHODS->match_url($uri))) {

				$classname = $match[1];
				$method = $match[2];
				$method_prefix = ZPHP::get_config('redirect_control_ajax_static_methods_prefix');

				self::$_IS_AJAX_CALL = true;

				$class_method = $method_prefix.$method;

				if(is_callable(array($classname, $class_method))  && self::_access_control($classname)) {
					call_user_func_array(array($classname, $class_method), array());
					die();
				}
				else
				{
					NavigationHelper::location_error_not_found();	
				}
			}

			if(($match = self::$_URL_PATTERN_AJAX_HANDLERS_CALL->match_url($uri))) {

				$classname = $match[1];
				$method = $match[2];
				
				self::$_IS_AJAX_CALL = true;

				if(is_subclass_of($classname, 'AjaxHandler')) {

					$obj = ClassHelper::create_instance_array($classname);

					if(is_callable(array($obj, $method)) && self::_access_control(get_class($obj))) {

						call_user_func_array(array($obj, $method), array());
					}

					$obj->out();
					die();
				}	
				else
				{
					NavigationHelper::location_error_not_found();
				}
			}

			if(ZPHP::get_config('under_construction'))
			{
				$page_control = ZPHP::get_config('redirect_control_special_pages_under_construction_page_control');

				$obj = ClassHelper::create_instance_array($page_control);

				if($obj) {

					if($obj instanceof MIMEControl && self::_access_control(get_class($obj))) {
						$obj->out();
						die();
					}
				}
			}

			if(ZPHP::get_config('under_maintenance'))
			{
				$page_control = ZPHP::get_config('redirect_control_special_pages_under_maintenance_page_control');

				$obj = ClassHelper::create_instance_array($page_control);

				if($obj) {

					if($obj instanceof MIMEControl && self::_access_control(get_class($obj))) {
						$obj->out();
						die();
					}
				}
			}

			if(ZPHP::is_mobile())
			{
				
				self::_redirect_process_uri_mobile($uri);

				if(LanguageHelper::is_enabled()) {

					list($language_parsed_uri, $language) = LanguageHelper::parse_language_url($uri);
					$uri = $language_parsed_uri;

					self::_redirect_process_uri_mobile($uri);
				}
			}
			
			self::_redirect_process_uri($uri);

			if(LanguageHelper::is_enabled()) {

				list($language_parsed_uri, $language) = LanguageHelper::parse_language_url($uri);
				$uri = $language_parsed_uri;

				self::_redirect_process_uri($uri);
			}

			LogFile::log_error_file('redirect', "Uri <{$uri}> not found");
			NavigationHelper::location_error_not_found();

		} catch (Exception $ex) {
			zphp_error_handler($ex);
		}
	}
	
	
	//----------------------------------------------------------------------------------
	
	public static function is_ajax_call()
	{
		return self::$_IS_AJAX_CALL;
	}
	
	/* Tambien se pueden pasar arrays */
	public static function add_redirects($pattern=null, $redirect=null, $id=null) {
	
		$args = func_get_args();
		call_user_func_array(array(self, '_add_redirects_urls'), $args);
	}
	
	/* Tambien se pueden pasar arrays */
	public static function add_redirects_mobile($pattern=null, $redirect=null, $id=null) {
	
		$args = func_get_args();
		call_user_func_array(array(self, '_add_redirects_mobile'), $args);
		
	}
	
	public static function redirect_process() {

		self::_redirect_process();
	}


}