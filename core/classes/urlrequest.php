<?php 

@ include_once(ZPHP::get_third_party_path('http_class/http_class.php'));
 
 class URLRequest {

	 const DEFAULT_METHOD = 'POST';
	 const AUTO_UPDATE_COOKIES = true;
	 const AUTO_REDIRECT = false;

	 //------------------------------------------------------------------------------------------------------------------------------------

	protected static function _flush()
	{
		ob_end_clean();
	}
	 
	 //------------------------------------------------------------------------------------------------------------------------------------
	 
	 protected $_data = array();
	 protected $_cookies = array();
	 protected $_url = '';
	 protected $_method = '';
	 protected $_user_agent = '';
	 protected $_response_cookies = array();
	 protected $_response_headers = array();
	 protected $_auto_update_cookies = false;
	 protected $_auto_redirect = false;
	 protected $_response_html = '';
	 	 
	 //------------------------------------------------------------------------------------------------------------------------------------

	 public function __construct($url=null, $data=array(), $cookie=array(), $method=null, $user_agent=null, $auto_update_cookies=null, $auto_redirect=null) {
		 
		 if(!$method) $method = self::DEFAULT_METHOD;
		 
		 $this->set_url($url);
		 $this->set_method($method);
		 $this->update_data($data);
		 $this->update_cookies($cookie);
		 $this->set_user_agent($user_agent ? $user_agent : $_SERVER['HTTP_USER_AGENT']);
		 $this->set_auto_update_cookies(is_null($auto_update_cookies) ? self::AUTO_UPDATE_COOKIES : $auto_update_cookies);
		 $this->set_auto_redirect(is_null($auto_redirect) ? self::AUTO_REDIRECT : $auto_redirect);
		 
	 } 
	 
	 
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	
	protected function _set_attr($name, $value) {
		switch($name) {
			case 'url': $this->set_url($value); break;
			case 'method': $this->set_method($value); break;
			case 'data': $this->set_data($value); break;
			case 'cookie': $this->set_cookie($value); break;
			case 'user_agent': $this->set_user_agent($value); break;
			case 'auto_update_cookies': $this->set_auto_update_cookies($value); break;
			case 'auto_redirect': $this->set_auto_redirect($value); break;
			
			
		}
	}
	
	
	
	protected function _get_attr($name) {
		switch($name) {
			case 'url': return $this->get_url(); break;
			case 'method': return $this->get_method(); break;
			case 'data': return $this->get_data(); break;
			case 'cookie': return $this->get_cookie(); break;
			case 'user_agent': return $this->get_user_agent(); break;
			case 'auto_update_cookies': return $this->get_auto_update_cookies(); break;
			case 'auto_redirect': return $this->get_auto_redirect(); break;
			case 'response_headers': return $this->get_response_headers(); break;
			case 'response_cookies': return $this->get_response_cookies(); break;
			case 'response_html': case 'html': return $this->get_response_html(); break;
						
			default: return null;
		}
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
		
	
	public function __set($name, $value=null) { return $this->_set_attr($name, $value); }
	
	
	public function __get($name) { return $this->_get_attr($name); }
	
	
	public function __toString() { return $this->_response_html; }

	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	
	/**
	 * 
	 * @return HTMLControl
	 */
	public function set_attr($arg1, $arg2=null){
		if(!is_array($arg1)) $attrs = array($arg1=>$arg2);
		else $attrs = $arg1;
		
		foreach($attrs as $key=>$value) 
			$this->_set_attr(self::_prepare_attr_name($key), $value);
					
		return $this;
	}
	
	
	public function get_attr($key){
		return $this->_get_attr(self::_prepare_attr_name($key));
	}
	
	/**
	 * 
	 * @return HTMLControl
	 */
	public function attr($arg1, $arg2=null){
		$args = func_get_args();
		$num_args = count($args); 
		if($num_args == 1 && !is_array($arg1)) return call_user_func_array(array($this,'get_attr'), $args);
		else return call_user_func_array(array($this,'set_attr'), $args);
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	 public function set_url($url) {

		 $this->_url = $url;
		 return $this;
		 
	 }
	 
	 
	 
	 public function get_url() {
		 
		 return $this->_url;
		 
	 }
	 
	 
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	public function url($arg=null){
		if(func_num_args() > 0) return $this->set_url($arg);
		else return $this->get_url();
	}
	 
	 
	 //------------------------------------------------------------------------------------------------------------------------------------
	
	 /**
	 * 
	 * @return URLRequest
	 */
	 public function set_method($method) {
		 
		 $this->_method = strtoupper(preg_replace('#[^\w]+#', '', $method));
		 return $this;
		 
	 }
	 
	 
	 
	 public function get_method() {
		 
		 return $this->_method;
		 
	 }
	 
	 
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	public function method($arg=null){
		if(func_num_args() > 0) return $this->set_method($arg);
		else return $this->get_method();
	}
	 
	 
	 
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	 public function set_user_agent($user_agent) {
		 
		 $this->_user_agent = $user_agent;
		 return $this;
		 
	 }
	 
	 
	 
	 public function get_user_agent() {
		 
		 return $this->_user_agent;
		 
	 }
	 
	 
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	public function user_agent($arg=null){
		if(func_num_args() > 0) return $this->set_user_agent($arg);
		else return $this->get_user_agent();
	}
	 
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	 public function set_auto_update_cookies($value) {
		 
		 $this->_auto_update_cookies = $value;
		 return $this;
		 
	 }
	 
	 
	 
	 public function get_auto_update_cookies() {
		 
		 return $this->_auto_update_cookies;
		 
	 }
	 
	 
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	public function auto_update_cookies($arg=null){
		if(func_num_args() > 0) return $this->set_auto_update_cookies($arg);
		else return $this->get_auto_update_cookies();
	}
	 
	
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	 public function set_auto_redirect($value) {
		 
		 $this->_auto_redirect = $value;
		 return $this;
		 
	 }
	 
	 
	 
	 public function get_auto_redirect() {
		 
		 return $this->_auto_redirect;
		 
	 }
	 
	 
	 
	 /**
	 * 
	 * @return URLRequest
	 */
	public function auto_redirect($arg=null){
		if(func_num_args() > 0) return $this->set_auto_redirect($arg);
		else return $this->get_auto_redirect();
	}
	 
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
		
	/**
	 * 
	 * @return URLRequest
	 */
	public function set_data($name=null, $value=null) {
		
		if(is_array($name)) return $this->update_data($name);
		else return $this->update_data(array($name => $value));
	}
	
	
	public function get_data($name=null, $default=null) {
		
		if($name) return array_key_exists($name, $this->_data) ? $this->_data[$name] : $default;
		else return array_slice ($this->_data, 0);
		
	}
		
	
	
	public function has_data($name) { 
		return array_key_exists($name, $this->_data); 
	}
	
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function remove_data($name) {
		foreach(func_get_args() as $names)
			foreach((array) $names as $name)
				unset($this->_data[$name]);
				
		return $this;
		
	}
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function clear_data() {
		$this->_data = array();
		return $this;
	}
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function update_data($data=null){
		foreach(func_get_args() as $data)
			if($data) {
			
				foreach(CastHelper::to_array($data) as $name => $value) {
					if(is_null($value)) unset($this->_data[$name]);
					else $this->_data[$name] = $value;
				}
			}
		
		return $this;
	}
	
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function data($arg1, $arg2=null) {
		
		$num_args = func_num_args();
		
		if($num_args == 1 && is_array($arg1)) return $this->update_data($arg1);
		
		else if($num_args == 1) return $this->get_data($arg1);
		
		else return $this->set_data($arg1, $arg2);
	}
	
	
	
	
	 
	 
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function set_cookie($name, $value=null) {
		
		if(is_array($name)) return $this->update_cookies($name);
		else return $this->update_cookies(array($name => $value));
		
	}
	
	
	public function get_cookie($name=null, $default=null) {
		
		if($name) return array_key_exists($name, $this->_cookies) ? $this->_cookies[$name] : $default;
		else return array_slice ($this->_cookies, 0);
		
	}
	
	
	
	public function get_cookies() {
		
		return array_slice ($this->_cookies, 0);
		
	}
		
	
	
	public function has_cookie($name) { 
		return array_key_exists($name, $this->_cookies); 
	}
	
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function remove_cookie($name) {
		foreach(func_get_args() as $names)
			foreach((array) $names as $name)
				unset($this->_cookies[$name]);
				
		return $this;
		
	}
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function clear_cookie() {
		$this->_cookies = array();
		return $this;
	}
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function update_cookies($cookie=null){
		
		foreach(func_get_args() as $cookie)
		
			if($cookie)
			
				foreach(CastHelper::to_array($cookie) as $name => $value) {
				
					if(is_null($value)) {
						
						unset($this->_cookies[$name]);
						
					} else {
			
						if(is_array($value)) {
							
							$cookie = $value;
							
						} else {
							
							$cookie = array('value' => $value);
							
						}
						
						$cookie['name'] = $name;
						
						$this->_cookies[$name] = $cookie;
						
					}
					
				}
		
		return $this;
	}
	
	
	/**
	 * 
	 * @return URLRequest
	 */
	public function cookie($arg1, $arg2=null) {
		
		$num_args = func_num_args();
		
		if($num_args == 1 && is_array($arg1)) return $this->update_cookies($arg1);
		
		else if($num_args == 1) return $this->get_cookie($arg1);
		
		else return $this->set_cookie($arg1, $arg2);
	}
	
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function get_header($name=null, $default=null) {
		
		if($name) return array_key_exists($name, $this->_response_headers) ? $this->_response_headers[$name] : $default;
		else return array_slice ($this->_response_headers, 0);
		
	}
		
	
	
	public function has_header($name) { 
		return array_key_exists($name, $this->_response_headers); 
	}
	
	
	
	
	
	public function get_request_header($name=null, $default=null) {
		
		if($name) return array_key_exists($name, $this->_response_headers) ? $this->_response_headers[$name] : $default;
		else return array_slice ($this->_response_headers, 0);
		
	}
		
	
	
	public function has_request_header($name) { 
		return array_key_exists($name, $this->_response_headers); 
	}
	
	
	public function get_response_headers() {
		
		return array_slice ($this->_response_headers, 0);
		
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function get_response_cookie($name=null, $default=null) {
		
		if($name) return array_key_exists($name, $this->_response_cookies) ? $this->_response_cookies[$name] : $default;
		else return array_slice ($this->_response_cookies, 0);
		
	}
		
	
	
	public function has_response_header($name) { 
		return array_key_exists($name, $this->_response_cookies); 
	}
	
	
	
	public function get_response_cookies() {
		
		return array_slice ($this->_response_cookies, 0);
		
	}
	
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function get_response_html() {
		
		return $this->_response_html;
		
	}

	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	
	
	public function request_url($url=null, $data=array(), $cookie=array(), $method=null, $user_agent=null, $auto_update_cookies=null, $auto_redirect=null) {
		
		$request_body = '';
		$request_headers = '';
		
		$this->_response_headers = array();
		$this->_response_cookies = array();
		
		$old_url = $this->_url;
		$old_method = $this->_method;
		$old_data = $this->_data;
		$old_cookie = $this->_cookies;
		$old_user_agent = $this->_user_agent;
		$old_auto_update_cookies = $this->_auto_update_cookies;
		$old_auto_redirect = $this->_auto_redirect;
		
		if($url) $this->set_url($url);
		if($method) $this->set_method($method);
		if($data) $this->set_data($data);
		if($cookie) $this->set_cookie($cookie);
		if($user_agent) $this->set_user_agent ($user_agent);
		if(!is_null($auto_update_cookies)) $this->set_auto_update_cookies($auto_update_cookies);
		if(!is_null($auto_redirect)) $this->set_auto_redirect($auto_redirect);
		
		$arguments = array();
		$arguments['PostValues'] = $this->_data;

		
		$http=new http_class;
		$http->user_agent = $this->_user_agent;
		$http->request_method = $this->_method;

		$url = $this->_url;
		
		if($this->_method != 'POST') {
		
			$data_html_query =urldecode(http_build_query($this->_data));
			
			$url = $url . (strpos($url, '?') === false ? '?' : '&' ) . $data_html_query;
			
		}


		
		foreach((array) $this->_cookies as $cookie) {
			
			if(!$cookie['expires']) $cookie['expires'] = "";
		
			if(!$cookie['path']) $cookie['path'] = "/";

			if(!$cookie['domain']) {

				if(preg_match('#^(?i)(\w+\:\/\/)?(?P<domain>(.+?))((\/|\\?).+)?$#', $url, $domain_match) > 0) {


					$cookie['domain'] = $domain_match['domain'];

				}

			}

		
			$http->SetCookie($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain']);
			
		}

		$error = $http->GetRequestArguments($url, $arguments);

		$arguments['host'] = $url;

		self::_flush();

		$error=$http->Open($arguments);

		$headers = array();

		if($error=="") {

			if($this->_method == 'POST') {
			
				$arguments['PostValues'] = $this->_data;
				
			}			
		
			$error=$http->SendRequest($arguments);

			$error=$http->ReadReplyHeaders($headers);

			if($error=="") {
				
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++) {
					
					$header_name=Key($headers);
					
				}
			}

			self::_flush();

			if($error=="") {
				
					for(;;) {
						
						$error=$http->ReadReplyBody($body,1000);

						if($error!="" || strlen($body)==0) break;
					
						$request_body.= $body;
												
					}
			}
			
			$http->Close();
			
		}
		
		foreach($headers as $key => $value) {
			
			$this->_response_headers[ucwords($key)] = $value;
			
		}

		foreach((array) $this->_response_headers['Set-cookie'] as $cookie_str) {
		
			$cookie = array();
			$cookie_key = '';
			
			foreach(explode(";", StringHelper::trim($cookie_str)) as $index => $cookie_part) {
				
				$cookie_part = StringHelper::trim($cookie_part);
				
				if(strpos($cookie_str, "=") !== false) {

					list($key, $value) = explode("=", $cookie_part, 2);
					
					if($index == 0) {

						$cookie['value'] = $value;
						$cookie['name'] = $key;
						$cookie_key = $key;
						
					} else {
						
						$cookie[$key] = $value;
						
					}
				
					
				}
				
			}
			$this->_response_cookies[$cookie['name']] = $cookie;
		}
		
		
		if($this->_auto_update_cookies) {
			
			$this->update_cookies($this->_response_cookies);
						
		}

		$this->_response_html = $request_body;

		if($this->_auto_redirect && $this->_response_headers['Location']) {
			return $this->request_url($this->_response_headers['Location'], $this->_data, $this->_cookies, $this->_method, $this->_user_agent, $this->_auto_update_cookies, $this->_auto_redirect);
		}


		$this->_url = $old_url;
		$this->_method= $old_method;
		$this->_data= $old_data;
		$this->_cookies = $old_cookie;
		$this->_user_agent = $old_user_agent;
		$this->_auto_update_cookies = $old_auto_update_cookies;
		$this->_auto_redirect = $old_auto_redirect;

		if($this->_auto_update_cookies) {
			
			$this->update_cookies($this->_response_cookies);
						
		}

		return $this->get_response_html();
		
	}
	
	
	
	public function request_redirect_url($data=array(), $cookie=array(), $method=null, $user_agent=null, $auto_update_cookies=null, $auto_redirect=null) {
		
		
		if($this->_auto_redirect && $this->_response_headers['Location']) {
			
			
			return $this->request_url($this->_response_headers['Location'], $data, $cookies, $method, $user_agent, $auto_update_cookies, $auto_redirect);
		
		} else {
			
			return '';
			
		}
		
	}
	
	
 }
 
