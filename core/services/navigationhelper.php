<?php 

class NavigationHelper {



	/*-------------------------------------------------------------*/

	public static function get_current_url() {
		return $_SERVER['REQUEST_URI'];
	}

	public static function get_current_url_abs() {
		$url = self::get_current_url();
		return self::conv_abs_url($url);
	}
	
	
	//-------------------------------------------------------------------------


	public static function header_content_type($type) {

		if($type == 'text/html') return NavigationHelper::header_content_text_html();
		else if($type == 'text/plain') return NavigationHelper::header_content_text_plain();
		else if($type) { @ header("Content-Type: {$type}"); }
	}
	
	
	public static function header_content_text_html($charset=null){
		if($charset==null) $charset = ZPHP::get_config('charset');
		@ header("Content-Type: text/html; charset=\"{$charset}\"");
	}
	
	
	public static function header_content_text_plain($charset=null){
		if($charset==null) $charset = ZPHP::get_config('charset');
		@ header("Content-Type: text/plain; charset=\"{$charset}\"");
		
	}
	
	
	public static function header_content_text_javascript($charset=null){
		if($charset==null) $charset = ZPHP::get_config('charset');
		@ header("Content-Type: text/javascript; charset=\"{$charset}\"");
		
	}
	
	
	
	public static function header_content_text_css($charset=null){
		if($charset==null) $charset = ZPHP::get_config('charset');
		@ header("Content-Type: text/css; charset=\"{$charset}\"");
		
	}
	
	
	public static function header_content_text($type, $charset=null) {
		if($charset==null) $charset = ZPHP::get_config('charset');
			
		$type_parts = explode('/',preg_replace('/[^\w\/\-\.]+/', '', strtolower($type)));
		$text_type = count($type_parts) > 0 ? array_pop($type_parts) : 'plain';
		
		@ header("Content-Type: text/{$text_type}".($charset ? "; charset=\"".preg_replace('/[^\w\/\-\.]+/', '', $charset)."\"" : ''));
		
	}
	
	
	/* dispositions: attachment, inline */
	public static function header_content_disposition($disposition, $filename=null){
		@ header("Content-Disposition: {$disposition}" . ($filename ? "; filename=\"".str_replace( array("\n", '"') ,'',basename($filename))."\"" : '' ) );
	}
	
	
	public static function header_content_attachment($filename){
		return NavigationHelper::header_content_disposition('attachment', $filename);
	}
	
	
	public static function header_content_length($length){
		$length = (integer) $length;
		@ header("Content-Length: {$length}");
	}
	
	
	
	public static function header_cache_control($control){
		@ header("Cache-Control: {$control}");
	}
	
	
	
	public static function header_cache_revalidate(){
		return NavigationHelper::header_cache_control("no-cache, must-revalidate");
	}
	
	
	public static function header_cache_enable($minutes) {
		
		$seconds = $minutes*60;
		$last_modified = new Date(2000, 1, 1);
		
		@ header("Pragma: public");
		@ header("Cache-Control: max-age=$seconds, public");
		@ header("User-Cache-Control: max-age=$seconds");
		@ header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified->get_unix_time()) . ' GMT');
	}
	
	
	
	//-------------------------------------------------------------------------
	
	public static function content_file_out($file, $attachment=false, $mimetype=null){
		
		if(is_object($file)) {
			
			
			if($file instanceof MIMEContent) {
				if($attachment) $file->out_attachment(is_string($attachment) ? $attachment : null);
				else $file->out();
				
			} else return NavigationHelper::content_out(strval($file), $mimetype, $attachment);
			
			
		} else {
		
			if(!$mimetype) { 
				if($attachment && is_string($attachment) && ($attachment_mimetype = FilesHelper::file_get_mimetype($attachment)))  $mimetype = $attachment_mimetype;
				else $mimetype = FilesHelper::file_get_mimetype($file);
			}
			
			NavigationHelper::header_content_type($mimetype);
			
			if($attachment) NavigationHelper::header_content_attachment(is_string($attachment) ? $attachment : $file);
			@ $filesize = filesize($file);
			NavigationHelper::header_content_length($filesize);
			
			FilesHelper::file_passthru($file);
		
		}
	}
	
	
	public static function content_out($content, $mimetype=null, $attachment=false){
		
		if(is_object($content)) return NavigationHelper::content_file_out($content, $attachment, $mimetype);
		else {
	
			if(!$mimetype && $attachment && is_string($attachment) && ($attachment_mimetype = FilesHelper::file_get_mimetype($attachment)))  $mimetype = $attachment_mimetype;
				
			NavigationHelper::header_content_type($mimetype);
			if($attachment) NavigationHelper::header_content_attachment(is_string($attachment) ? $attachment : 'attachment' . ($mimetype ? MimeTypeHelper::get_extension(MimeTypeHelper::mimetype($mimetype)) : ''));
			NavigationHelper::header_content_length(strlen($content));
			
			echo $content;
		}
	}
	
	//-------------------------------------------------------------------------
	
	public static function cookie_add($name, $value=null, $time=86400, $path='/', $domain=null, $secure=null, $httponly=true){
		try{ 
			@ setcookie($name, $value, time()+$time, $path, $domain, $secure, $httponly);
			return true;
		} catch(Exception $e) { return false; }
	}
	
	
	public static function cookie_add_days($name, $value=null, $days=30, $path='/', $domain=null, $secure=null, $httponly=true){
		NavigationHelper::cookie_add($name, $value, $days*24*3600, $path, $domain, $secure, $httponly);
	}
	
	
	public static function cookie_remove($names, $path=null, $domain=null, $secure=null, $httponly=null){
		$expires=time()-3600;
		try{
			foreach(((array) $names) as $name) @ setcookie($name, null, $expires, $path, $domain, $secure, $httponly); 
			return true;
		} catch(Exception $e) { return false; }
	}
	
	
	public static function cookie_add_session($name, $value=null, $path=null, $domain=null, $secure=null, $httponly=null){
		try{ 
			@ setcookie($name, $value, 0, $path, $domain, $secure, $httponly); 
			return true;
		} catch(Exception $e) { return false; }
	}
	
	//-------------------------------------------------------------------------

	protected static $_PATH_INFO = null;
	
	protected static function _get_path_info() {
		
		if(is_null(self::$_PATH_INFO)) {
			self::$_PATH_INFO = array_values(array_filter(explode('/',$_SERVER['PATH_INFO'])));
		}
		
		return self::$_PATH_INFO;
	}
	
	public static function location_get_path_info($all=true){
		if($all) return array_values(array_filter(explode('/',$_SERVER['PATH_INFO'])));
		else return self::_get_path_info();
	}
	
	
	public static function location_shift_path_info($default=null) {
		
		$path_info = self::_get_path_info();
		
		if(!empty($path_info)) return array_shift($path_info);
		else return $default;
	}
	
	
	public static function location_pop_path_info($default=null) {
		
		$path_info = self::_get_path_info();
		
		if(!empty($path_info)) return array_pop($path_info);
		else return $default;
	}
	
	
	public static function location_go($url, $use_document_path=true) {
		
		if(strpos($url, '/') === 0 && $use_document_path)
			$url = rtrim(ZPHP::get_config('site_document_path'), '/').'/'.ltrim($url, '/');
		
		
		if(!$url) $url = ZPHP::get_config('site_url');

		foreach(headers_list() as $header)
		{
			if(StringHelper::starts_with($header, 'content-type', true))
			{
				$url = self::conv_abs_url($url);
				die("<script type='text/javascript'>location.href=".JSHelper::cast_str($url).";</script>");
			}
		}

		header("Location: {$url}");
		exit();
	}

	public static function location_go_reverse($id, $arg1=null, $arg2=null) {

		$args = func_get_args();
		$url = call_user_func_array(array('URLPattern', 'reverse'), $args);
		self::location_go($url);

	}

	public static function location_go_page($page) {

		$url = HTMLPage::get_page_last_url($page);
		self::location_go($url);

	}

	public static function location_go_back(){
		return NavigationHelper::location_go($_SERVER['HTTP_REFERER']);	
	}
	
	
	public static function location_go_home(){
		return NavigationHelper::location_go(ZPHP::get_config('site_url'));	
	}
	
	
	public static function location_go_query($url, $data=null, $merge=true, $use_document_path=true) {
		return NavigationHelper::location_go(NavigationHelper::make_url_query($data, $url, $merge), $use_document_path);
	}
	
	
	public static function location_update_query($data=null, $merge=true, $use_document_path=true) {
		return NavigationHelper::location_go_query($_SERVER['REQUEST_URI'], $data, $merge, $use_document_path);
	}
	
	
	//-------------------------------------------------------------------------
	
	
	public static function conv_abs_url($url, $base_url=null){
		
		if(!$base_url) $base_url = ZPHP::get_config('site_url');
		
		$url = trim($url);
		$base_url = trim($base_url, '/');
		
		if(preg_match('#^\w+\:\/\/.+#', $url) == 1) return $url;
		else if($base_url) return $base_url.'/'.ltrim ($url, '/');
		else return $url;
	}
	
	
	public static function conv_abs_url_path($path, $document_root=null){
		
		if(!$document_root) $document_root = ZPHP::get_config('www_dir');
		
		if(($path = trim($path)) && (strpos($path, '/')===0 || strlen($path)==0) && ($document_root = rtrim(trim($document_root), '/'))) return $document_root.'/'.substr($path, 1);
		else return $path;
	}
	
	
	
	public static function conv_external_url($url, $default_protocol='http') {
		
		$url = trim($url);
		if(!((boolean) preg_match('#^(\w\:\/\/).+$#', $url))) $url = "{$default_protocol}://{$url}";
		
		return $url;
	}
	
	
	public static function make_url_query($data, $url=null, $merge=true, $clear_null=true, $abs=false) {
		
		if(is_null($url)) $url = self::get_current_url();
		
		list($url, $url_query_string) = explode('?', $url, 2);
		
		if($merge && $url_query_string) {
			
			parse_str($url_query_string, $url_query_data);
			if(is_string($data)) parse_str($data, $data);
				
			return NavigationHelper::make_url_query(array_merge($url_query_data, $data), $url, false, $clear_null, $abs);
			
		}
		
		
		if(!is_string($data)) {
		
			if(is_array($data) && $clear_null) {
		
				foreach($data as $key => $value) {
	
					if(is_null($value) || $value === '') {
						unset($data[$key]);
					}
	
				}
				
			}
			
			$data = http_build_query($data);
	
		}
		
		
		$url = $url.($data ? '?' : '').$data;

		if($abs)
		{
			$url = self::conv_abs_url($url);
		}

		return $url;
	}


	public static function make_url_query_abs($data, $url=null, $merge=true, $clear_null=true) {

		return self::make_url_query($data, $url, $merge, $clear_null, true);
	}
	
	public static function url_parse_query_vars($url, &$new_url=null) {
		
		list($url, $url_query_string) = explode('?', $url, 2);
		
		$new_url = $url;
		
		if($url_query_string) {
			
			parse_str($url_query_string, $url_query_data);
			if(is_string($data)) parse_str($data, $data);
				
			return $url_query_data;
			
		} else {
			
			return array();
			
		}
		
	}
	
	
	//-------------------------------------------------------------------------
	
	
	protected static $_NAVIGATION_HISTORY_DEFINED = false;
	protected static $_NAVIGATION_HISTORY_SESSION_VARNAME = 'navigation_history';
	
	const NAVIGATION_HISTORY_DEFAULT_URL = '';
	const NAVIGATION_HISTORY_BACK_URL = '_goback';
	const NAVIGATION_HISTORY_BACK_URL_VARNAME = 'url';

	public static function navigation_history_register_url($mantain_query=null, $url=null) {
			
		SessionHelper::init();
		
		if(self::$_NAVIGATION_HISTORY_DEFINED) return;
		
		if(is_null($url)) $url = self::get_current_url();
		
		$url = trim($url, '/');
		
		list($url_base, $url_query_string) = explode('?', $url, 2);
		
		$url_base = trim($url_base, '/');
		
		parse_str($url_query_string, $url_query_data);
		
		$mantain_query = (array) $mantain_query;
		
		$query_data = http_build_query(ArrayHelper::select_keys($url_query_data, $mantain_query));
		
		$url_key = $url_base.($query_data ? '?' : '').$query_data;
		
		$url_data = array('url' => $url, 'mantain_query' => $mantain_query, 'key' => $url_key);

		$history = array_filter((array) $_SESSION[self::$_NAVIGATION_HISTORY_SESSION_VARNAME]);
			
		if(count($history) > 0) {
		
			$last_url = $history[0];
			
			if($url_key != $last_url['key']) array_unshift($history, $url_data);
			
			else {
				
				$last_url['url'] = $url;
				
				$history[0] = $last_url;
			}
				
		} else array_unshift($history, $url_data);
		

		$_SESSION[self::$_NAVIGATION_HISTORY_SESSION_VARNAME] = array_slice($history, 0, 15);
		
		self::$_NAVIGATION_HISTORY_DEFINED = true;
	}
	
	
	public static function navigation_history_get_back_url($for_href=false) {

		SessionHelper::init();
		
		$history = array_filter((array) $_SESSION[self::$_NAVIGATION_HISTORY_SESSION_VARNAME]);
		
		$index = self::$_NAVIGATION_HISTORY_DEFINED ? 1 : 0;

		if($for_href)
		{
			return NavigationHelper::make_url_query(array(self::NAVIGATION_HISTORY_BACK_URL_VARNAME => $history[$index] ? $history[$index]['url'] : self::NAVIGATION_HISTORY_DEFAULT_URL), self::NAVIGATION_HISTORY_BACK_URL);
		}
		else
		{
			return $history[$index] ? $history[$index]['url'] : self::NAVIGATION_HISTORY_DEFAULT_URL;
		}

		
	}
	
	
	public static function navigation_history_go_back($url=null) {

		SessionHelper::init();
		
		$history = array_filter((array) $_SESSION[self::$_NAVIGATION_HISTORY_SESSION_VARNAME]);
		
		if(is_null($url)) $url = $_SERVER['HTTP_REFERER'];
		
		$url = trim($url, '/');
				
		list($url_base, $url_query_string) = explode('?', $url, 2);
	
		$url_base = trim($url_base, '/');
	
		parse_str($url_query_string, $url_query_data);
	
		
		$offset_index = -1;
		
		for($index=0; $index<count($history); $index++) {
			
			$history_url_data = $history[$index];
	
			$mantain_query = (array) $history_url_data['mantain_data'];
		
			$query_data = http_build_query(ArrayHelper::select_keys($url_query_data, $mantain_query));
		
			$url_key = $url_base.($query_data ? '?' : '').$query_data;
		
			if($url_key == $history_url_data['key']) {
				
				$offset_index = $index;
				break;
			}
		
		}
		
		if($offset_index >= 0) {
			
			$history = array_slice($history, $offset_index);
			$_SESSION[self::$_NAVIGATION_HISTORY_SESSION_VARNAME] = $history;
		}
		
		
		NavigationHelper::location_go($url);
	}
	
	//-------------------------------------------------------------------------
	
	
	
	public static function navigation_is_mobile() {
		
		//return true;
		// Get the user agent
		
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
		// Create an array of known mobile user agents
		// This list is from the 21 October 2010 WURFL File.
		// Most mobile devices send a pretty standard string that can be covered by
		// one of these.  I believe I have found all the agents (as of the date above)
		// that do not and have included them below.  If you use this function, you 
		// should periodically check your list against the WURFL file, available at:
		// http://wurfl.sourceforge.net/
	
	
		$mobile_agents = Array(
	
	
			"240x320", 	"acer", "acoon", "acs-", "abacho", "ahong", "airness", "alcatel", "amoi",	
			"android", "anywhereyougo.com", "applewebkit/525", "applewebkit/532", "asus", "audio",
			"au-mic", "avantogo", "becker", "benq", "bilbo", "bird", "blackberry", "blazer", "bleu", 
			"cdm-", "compal", "coolpad", "danger", "dbtel", "dopod", "elaine", "eric", "etouch", "fly " , 
			"fly_", "fly-", "go.web", "goodaccess", "gradiente", "grundig", "haier", "hedy", "hitachi", 
			"htc", "huawei", "hutchison", "inno", "ipad", "ipaq", "ipod", "jbrowser", "kddi", "kgt", 
			"kwc", "lenovo", "lg ", "lg2", "lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", "lge-", 
			"lge9", "longcos", "maemo", "mercator", "meridian", "micromax", "midp", "mini", "mitsu", 
			"mmm", "mmp", "mobi", "mot-", "moto", "nec-", "netfront", "newgen", "nexian", "nf-browser", 
			"nintendo", "nitro", "nokia", "nook", "novarra", "obigo", "palm", "panasonic", "pantech", 
			"philips", "phone", "pg-", "playstation", "pocket", "pt-", "qc-", "qtek", "rover", 
			"sagem", "sama", "samu", "sanyo", "samsung", "sch-", "scooter", "sec-", "sendo", "sgh-", 
			"sharp", "siemens", "sie-", "softbank", "sony", "spice", "sprint", "spv", "symbian", 
			"tablet", "talkabout", "tcl-", "teleca", "telit", "tianyu", "tim-", "toshiba", "tsm", 
			"up.browser", "utec", "utstar", "verykool", "virgin", "vk-", "voda", "voxtel", "vx", 
			"wap", "wellco", "wig browser", "wii", "windows ce", "wireless", "xda", "xde", "zte"
		);
	
		// Pre-set $is_mobile to false.
	
		$is_mobile = false;
	
		// Cycle through the list in $mobile_agents to see if any of them
		// appear in $user_agent.
	
		foreach ($mobile_agents as $device) {
	
			// Check each element in $mobile_agents to see if it appears in
			// $user_agent.  If it does, set $is_mobile to true.
	
			if (striStringHelper::str($user_agent, $device)) {
	
				$is_mobile = true;
	
				// break out of the foreach, we don't need to test
				// any more once we get a true value.
	
				break;
			}
		}
	
		return $is_mobile;
	
	}
	
	
	
	//-------------------------------------------------------------------------

	public static function location_error_not_found($not_found_page_url=null) {

		if(!$not_found_page_url) $not_found_page_url = ZPHP::get_actual_uri();
		
		$not_found_page_control_classname = ZPHP::get_config('redirect_control_special_pages_not_found_page_control');
		
		if($not_found_page_control_classname)
		{
			$not_found_page_control = ClassHelper::create_instance($not_found_page_control_classname);
		}
		else
		{
			$not_found_page_control = null;
		}
		
		header("HTTP/1.0 404 Not Found");
		
		if($not_found_page_control)
		{
			$not_found_page_control->out();
		}
		
		die();
	}
	
	
	//-------------------------------------------------------------------------
	
	
	public static function get_requested_uri() {
		return $_SERVER['REQUEST_URI'];
	}
	
	
	//-------------------------------------------------------------------------

	public static function test_logged_http_basic_auth_users($users)
	{
		if(is_array($users))
		{
			foreach($users as $user)
			{
				if(self::test_logged_http_basic_auth_users($user))
				{
					return true;
				}
			}

			return false;
		}

		return isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $users;
	}

	public static function test_logged_http_basic_auth_admin($test_development=true)
	{
		if(ZPHP::is_development_mode())
		{
			return true;
		}

		if($test_development && self::test_logged_http_basic_auth_development(true))
		{
			return true;
		}

		return self::test_logged_http_basic_auth_users(ZPHP::get_config('access_control.admin.user'));
	}

	public static function test_logged_http_basic_auth_development($test_master=true)
	{
		if(ZPHP::is_development_mode())
		{
			return true;
		}

		if($test_master && self::test_logged_http_basic_auth_master())
		{
			return true;
		}

		return self::test_logged_http_basic_auth_users(ZPHP::get_config('access_control.development.user'));
	}

	public static function test_logged_http_basic_auth_master()
	{

		if(ZPHP::is_development_mode())
		{
			return true;
		}

		return self::test_logged_http_basic_auth_users(ZPHP::get_config('access_control.master.user'));
	}

	
	public static function test_http_basic_auth_users(array $users) {
		
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			
			$user = $_SERVER['PHP_AUTH_USER'];
			
			if (isset($_SERVER['PHP_AUTH_PW'])) {
				
				$pass = $_SERVER['PHP_AUTH_PW'];
				
			} else {
				
				$pass = null;
				
			}
			
			foreach((array) $users as $user_data)
			{
				if(is_array($user_data) && isset($user_data['user']))
				{
					if($user_data['user'] == $user && $user_data['password'] == $pass)
					{
						return true;
					}
				}
			}
		}
	}
	
	public static function require_http_basic_auth_users(array $users, $text='Login') {

		header('WWW-Authenticate: Basic realm="'.HTMLHelper::escape($text).'"');
		
		
		if (isset($_SERVER['PHP_AUTH_USER'])) {
			
			$user = $_SERVER['PHP_AUTH_USER'];
			
			if (isset($_SERVER['PHP_AUTH_PW'])) {
				
				$pass = $_SERVER['PHP_AUTH_PW'];
				
			} else {
				
				$pass = null;
				
			}
			
			foreach((array) $users as $user_data)
			{
				if(is_array($user_data) && isset($user_data['user']))
				{
					if($user_data['user'] == $user && $user_data['password'] == $pass)
					{
						header('HTTP/1.0 200 OK');
						return true;
					}
				}
			}
		}

		if(!isset($_SERVER['PHP_AUTH_USER']))
		{
			header('HTTP/1.0 401 Unauthorized');
			header('Status: 401 Unauthorized');
			die();
		}
	}
	
	
	public static function require_http_basic_auth_user($user, $password, $text='Login') {
		
		return self::require_http_basic_auth_users(array('user' => $user, 'password' => $password), $text);
		
	}

	public static function require_http_basic_auth_logout($redirect_url=null)
	{
		if(!$redirect_url)
		{
			$redirect_url = ZPHP::get_site_url();
		}
		
		header('HTTP/1.0 401 Unauthorized');
		header('Status: 401 Unauthorized');
		self::location_go($redirect_url);
	}
	//-------------------------------------------------------------------------
	
	public static function get_url_headers($url, $normalize_keys=true)
	{
		try
		{
			$headers = get_headers($url);
			
			foreach($headers as $key => $value)
			{
				unset($headers[$key]);
				
				if(preg_match('#^(?i)\s*(?P<key>.+?)\s*\:\s*(?P<value>.+?)\s*$#', $value, $match))
				{
					$header_key = $match['key'];
					$header_value = $match['value'];
					
					if($normalize_keys)
					{
						$header_key = strtoupper($header_key);
					}
					
					$headers[$header_key] = $header_value;
				}
			}
			
		} 
		catch(Exception $ex) 
		{
			$headers = array();
		}
		
		return $headers;
		
	}

	//-------------------------------------------------------------------------

	public static function save_page_message($text, $classname=null)
	{
		try{
			@ setcookie('__pageMessage__', $text, time()+8400, '/');
//			@ setcookie('__pageMessage__[classname]', $classname, time()+8400);
			return true;
		} catch(Exception $e) { return false; }
	}
}
