<?php

/* ZFramework Version 1.0 */

/* Clase principal que cargará las configuraciones de config.xml */
/* Se deben tener definidas por apache (ej .htaccess las variables):
 * ZFRAMEWORK_APP_DIR
 * 
 */

class ZPHP {

	const DEBUG_TYPE_DB = 'db';
	const DEBUG_TYPE_CUSTOM = 'custom';

	/*-------------------------------------------------------------*/

	/* Si se debe hacer utf8_decode de las variables del config.xml */
	private static $_DECODE_UTF8 = false;

	/* Separador para valores array */
	private static $_ARRAY_SEPARATOR = '::';

	/* Prefijo para valores array */
	private static $_ARRAY_PREFIX = 'ARRAY|';

	/* Variable que contendr todo el config (cuando sea seteado) */
	private static $_CONFIG = null;
	
	/* Nombre del archivo de configuracion */
	private static $_CONFIG_FILENAME = 'config.xml';
	
	private static $_CRON_COMMAND = 'cron';
	
	private static $_INITIAL_CONFIG_LOADED = false;
	
	private static $_IS_MOBILE = null;
	private static $_IS_TABLET = null;

	private static $_DEBUG_MODE = null;
	private static $_DEVELOPMENT_MODE = null;
	private static $_LIVE_MODE = null;

	private static $_DEBUG_DATA = null;
	private static $_DEBUG_DATA_SESSION_REMEMBER_VARNAME = '__zframework_remember_debug_data__';
	private static $_DEBUG_DATA_SESSION_VARNAME = '__zframework_debug_data__';

	private static $_REQUEST_ID = null;

	/*---------------------------------------------------------*/
	
	protected static function _load_config($config_file) {
		
		$xml = simplexml_load_file($config_file);
		
		if(!$xml || !is_object($xml) || get_class($xml) != 'SimpleXMLElement') die();

		self::$_CONFIG->_set_data($xml);
	}
	
	/*---------------------------------------------------------*/
	
	/* @return ZPHP */
	public static function get_config($key=null, $default=null) {
		
		if(func_num_args() > 0) {
			return self::$_CONFIG->get_value($key, $default);
		} else {
			return self::$_CONFIG;
		}
	}
	
	public static function load_initial_config($app_dir) {

		if(!self::$_INITIAL_CONFIG_LOADED)
		{
			$app_dir = rtrim($app_dir, '/');

			self::$_CONFIG = new ZPHP();
			self::$_CONFIG->_set_value('zframework_dir', dirname(__FILE__));
			self::$_CONFIG->_set_value('site_dir', $app_dir);
			self::$_CONFIG->_set_value('app_dir', $app_dir);
			self::$_CONFIG->_set_value('backend_dir', self::$_CONFIG->get_value('site_dir').'/backend');
			self::$_CONFIG->_set_value('www_dir', file_exists(self::$_CONFIG->get_value('site_dir').'/www') ? self::$_CONFIG->get_value('site_dir').'/www' : self::$_CONFIG->get_value('site_dir'));
			self::$_CONFIG->_set_value('config_file', self::$_CONFIG->get_value('site_dir').'/config.xml');

			self::_load_config(dirname(__FILE__).'/config.default.xml');
			self::_load_config(ZPHP::get_config('config_file'));

			if(isset($_SERVER['SERVER_NAME']))
			{
				self::$_CONFIG->_set_value('server_name', $_SERVER['SERVER_NAME']);
			}
						
			$dirname = __DIR__;
			$config_file = null;
			
			while(true) {

				$filename = $dirname.'/'.self::$_CONFIG_FILENAME;

				if(file_exists($filename)) {

					$config_file = $filename;
					break;
					
				} else {
					
					$new_dirname = realpath(dirname(realpath($dirname)));
					
					if($new_dirname == $dirname) {
						break;
					} else {
						$dirname = $new_dirname;
					}
				}
				
			}
			
			if(!$dirname || $dirname == '/')
			{
				$dirname = $app_dir;
				$config_file = null;

				while(true) {

					$filename = $dirname.'/'.self::$_CONFIG_FILENAME;

					if(file_exists($filename)) {

						$config_file = $filename;
						break;

					} else {

						$new_dirname = realpath(dirname(realpath($dirname)));

						if($new_dirname == $dirname) {
							break;
						} else {
							$dirname = $new_dirname;
						}
					}

				}
			}

			if(!$dirname || $dirname == '/')
			{
				$dirname = $_SERVER['DOCUMENT_ROOT'];
			}

			while(true) {

				$filename = $dirname.'/'.self::$_CONFIG_FILENAME;

				if(file_exists($filename)) {

					$config_file = $filename;
					break;

				} else {

					$new_dirname = realpath(dirname(realpath($dirname)));

					if($new_dirname == $dirname) {
						break;
					} else {
						$dirname = $new_dirname;
					}
				}

			}

			if(!is_null($config_file)) {
				
				self::$_CONFIG->_set_value('main_config_file', $config_file);
				self::$_CONFIG->_set_value('main_config_dir', dirname($config_file));
				self::_load_config($config_file);				
				
			}
			else
			{
				die('No existe el archivo de configuración');
			}



			self::$_INITIAL_CONFIG_LOADED = true;
		}
	}
	
	public static function load_config($config_file) {
		self::_load_config($config_file);
	}
	
	//---------------------------------------------------------------------------------
	
	/* Funciones útiles */
	
	public static function get_third_party_path($filename=null) {
		return ZPHP::get_config('zframework_dir').'/core/thirdparty'.($filename ? ('/'.ltrim($filename, '/')) : '');
	}
	
	public static function get_site_name() {
		return ZPHP::get_config('site_name');
	}
	
	public static function get_abs_url($url) {
		return NavigationHelper::conv_abs_url($url);
	}
	
	public static function is_debug_mode() {

		if(is_null(self::$_DEBUG_MODE))
		{
			self::$_DEBUG_MODE = (bool) ZPHP::get_config('debug_mode', 0);
		}
		return self::$_DEBUG_MODE;
	}

	public static function is_live_mode() {

		if(is_null(self::$_LIVE_MODE))
		{
			self::$_LIVE_MODE = (bool) ZPHP::get_config('live_mode', 0);
		}
		return self::$_LIVE_MODE;
	}

	public static function is_development_mode() {

		if(is_null(self::$_DEVELOPMENT_MODE))
		{
			self::$_DEVELOPMENT_MODE = (bool) ZPHP::get_config('development_mode', 0);
		}
		return self::$_DEVELOPMENT_MODE;
	}

	public static function get_zframework_dir() {
		return ZPHP::get_config('zframework_dir');
	}
	
	public static function get_www_dir() {
		return ZPHP::get_config('www_dir');
	}
	
	public static function get_site_dir() {
		return realpath(ZPHP::get_config('site_dir'));
	}
	
	public static function get_app_dir() {


		if(!self::$_INITIAL_CONFIG_LOADED)
		{
			if(isset($_SERVER['ZFRAMEWORK_APP_DIR']))
			{
				return $_SERVER['ZFRAMEWORK_APP_DIR'];
			}
			else
			{
				$zframework_dir = dirname(__FILE__);
				$app_dir = $_SERVER['DOCUMENT_ROOT'];
				return $app_dir;
			}
		}
		else
		{
			return self::get_site_dir();
		}
	}
	
	public static function get_site_url($protocol=true) {
		
		$url = ZPHP::get_config('site_url');
		
		if($protocol) 
		{
			return $url;
		}
		else
		{
			return preg_replace('#^[\w]+\:\/\/#', '', $url);
		}
	}
	
	public static function get_actual_uri(array $vars=array()) {
		
		$uri = $_SERVER['REQUEST_URI'];
		$uri = StringHelper::remove_prefix($uri, ZPHP::get_config('site_document_path'));

		if(($query_pos = strpos($uri, '?')) !== false)
		{
			$uri_vars_str = substr($uri, $query_pos+1);
			$uri = substr($uri, 0, $query_pos);

			parse_str($uri_vars_str, $uri_vars);

		}
		else
		{
			$uri_vars = array();
		}

		$uri_vars = array_merge($uri_vars, $vars);

		if(!empty($uri_vars))
		{
			$uri.= '?'.http_build_query($uri_vars);
		}
		
		return $uri;
	}

	public static function get_absolute_actual_uri(array $vars=array()) {

		$uri = self::get_actual_uri($vars);
		$uri = self::get_site_url().$uri;

		return $uri;
	}
	
	
	/*---------------------------------------------------------*/
	
	public static function get_cron_command()
	{
		return self::$_CRON_COMMAND;
	}

	/*-------------------------------------------------------------*/

	public static function get_request_id()
	{
		if(!self::$_REQUEST_ID)
		{
			self::$_REQUEST_ID = uniqid('request');
		}

		return self::$_REQUEST_ID;
	}

	/*-------------------------------------------------------------*/

	public static function get_debug_data_remember()
	{
		return SessionHelper::get_var(self::$_DEBUG_DATA_SESSION_REMEMBER_VARNAME);
	}

	public static function set_debug_data_remember($remember)
	{
		return SessionHelper::add_var(self::$_DEBUG_DATA_SESSION_REMEMBER_VARNAME, $remember);
	}
	
	private static function _prepare_debug_data($request_id=null, $url=null)
	{
		if(is_null(self::$_DEBUG_DATA))
		{
			if(self::get_debug_data_remember())
			{
				self::$_DEBUG_DATA = (array) SessionHelper::get_var(self::$_DEBUG_DATA_SESSION_VARNAME);
			}
			else
			{
				self::$_DEBUG_DATA = array();
			}

		}

		if(!$request_id)
		{
			$request_id = self::get_request_id();
			$url = self::get_actual_uri();
		}


		if(!isset(self::$_DEBUG_DATA[$request_id]))
		{
			self::$_DEBUG_DATA[$request_id] = array(
				'url' => $url,
				'request' => $request_id,
				'items' => array(),
			);
		}

		self::_update_debug_data();
	}


	protected static function _update_debug_data()
	{
		if(self::get_debug_data_remember())
		{
			SessionHelper::add_var(self::$_DEBUG_DATA_SESSION_VARNAME, self::$_DEBUG_DATA);
		}
	}

	protected static function _can_add_debug_data()
	{
		return self::is_debug_mode() && preg_match('#(?i)zframework\.php#', self::get_actual_uri());
	}

	public static function clear_debug_data()
	{
		if(self::_can_add_debug_data() && preg_match('#(?i)zframework\.php#', $debug_data_request['url']))
		{
			self::$_DEBUG_DATA = array();
			self::_update_debug_data();
		}
	}

	public static function add_debug_data($data, $title=null, $type=null, $time=null)
	{

		if(self::_can_add_debug_data())
		{
			self::_prepare_debug_data();
			$request_id = self::get_request_id();

			if(!$title)
			{
				$title = 'Debug';
			}

			if(!$type)
			{
				$type = self::DEBUG_TYPE_CUSTOM;
			}

			self::$_DEBUG_DATA[$request_id]['items'][] = ['type' => $type, 'title' => $title, 'data' => (string) $data, 'time' => is_null($time) ? time() : $time,];

			self::_update_debug_data();
		}
	}

	public static function get_debug_data($request_id=null)
	{
		self::_prepare_debug_data($request_id);

		if($request_id)
		{
			return array_merge(self::$_DEBUG_DATA[$request_id]['items'], array());
		}
		else
		{
			$debug_data = array_reverse(array_filter(array_merge(self::$_DEBUG_DATA, array())));
			$filtered_debug_data = array();

			foreach($debug_data as $key => $debug_data)
			{
				if(!empty($debug_data['items']))
				{
					$filtered_debug_data[$key] = $debug_data;
				}
			}

			return $filtered_debug_data;
		}

	}

	
	/*---------------------------------------------------------*/
	
	private $_data;

	private function __construct() {
		$this->_data = array();
	}
	
	
	private function _get_parsed_value($value) {
		
		if(is_null($value)) {
			
			return null;

		} else if(is_array($value))	{
			
			$parsed_array = array();
			
			$value = array_filter($value);
			
			foreach($value as $v) {
				$parsed_array[] = $this->_get_parsed_value($v);
			}
			
			return $parsed_array;
			
		} else {
			
			while(preg_match('@\{(\w+)\}@', $value, $match)) {

				$replace_str = $match[0];
				$replace_constant_name = $match[1];
				$replace_constant_value = self::get_config($replace_constant_name);

				$value = str_ireplace($replace_str, $replace_constant_value, $value);
			}
			
			return $value;

		}
	}
	
	private function _set_value($key, $value, $merge_array=false) {

		$key = strtolower($key);
		
		if($merge_array && array_key_exists($key, $this->_data) && is_array($this->_data[$key])) {
			$value = array_merge($this->_data[$key], is_array($value) ? $value : array($value));
		}
		
		$this->_data[$key] = $value;
		$this->$key = $value; /* For be accessible as an array */
	}
	
	
	private function _set_data(SimpleXMLElement $xml, $constant_names_prefixs=array()) {
		
		$constants_names_prefix_str = strtoupper(implode('_', $constant_names_prefixs));
		
		if(count($constant_names_prefixs) > 0) $constants_names_prefix_str.= '_';
		
		foreach((array) $xml as $key => $value) {

			if($key == 'comment') continue;
			
			$value_is_xml = is_object($value) && get_class($value) == 'SimpleXMLElement';
			
			$value_is_array = false;
			
			$constant_name = $constants_names_prefix_str.strtoupper($key);

			if($value_is_xml && $value->count() > 0) {
				
				if(isset($this->_data[$key]) && $this->_data[$key] && is_object($this->_data[$key]) && get_class($this->_data[$key]) == 'ZPHP') {
					
					$value_xml = $this->_data[$key];
					$value_xml->_set_data($value, array_merge($constant_names_prefixs, array($value->getName())));
					$value = $value_xml;
					
				} else {

					$value_xml = new ZPHP();
					$value_xml->_set_data($value, array_merge($constant_names_prefixs, array($value->getName())));
					$value = $value_xml;
					
				}
		
			} else {

				if($value_is_xml) {
					
					$value = null;
					
				} else if(is_array($value)) {
					
					if(!empty($value)) $value = array_pop($value);
					else $value = null;
					
				}
				
				if(stripos($value, self::$_ARRAY_PREFIX) === 0) {
					$value_is_array = true;
					$value = substr($value, strlen(self::$_ARRAY_PREFIX));
				} else {
					$value_is_array = false;
				}

				$values = array();

				$array_parts = explode(self::$_ARRAY_SEPARATOR, $value);
				
				foreach($array_parts as $value) {

					$value = trim($value);
					
					if(preg_match('#\[(.+?)\]#', $value, $match)) {

						$replace_str = $match[0];

						foreach((array) explode(',', $match[1]) as $token) {

							$values[] = str_replace($replace_str, trim($token), $value);

						}

					} else {

						$values[] = $value;

					}

				}
					
				if(count($values) == 1 && !$value_is_array) {
					
					$value = $values[0];
					
					if(self::$_DECODE_UTF8) {
						$value = utf8_decode($value);
					}
					
				} else {
					
					$value = $values;
					
					if(self::$_DECODE_UTF8) {
						
						foreach($value as $i => $v) {
							$value[$i] = utf8_decode($v);
						}
						
					}
				}
				
			}
				
			$this->_set_value($key, $value, true);
			
		}
		
		
	}
	
	//-------------------------------------------------------------------------------
	
	public function __toArray() {
		return $this->get_array();
	}
	
	
	public function __get($name) {
		return $this->get_value($name);
	}
	
	public function __set_state() {
		return array_keys($this->_data);
	}
	
	//-------------------------------------------------------------------------------
	
	public function get_array() {
		return array_merge($this->_data);
	}
	
	
	public function get_value($key, $default=null) {
		
		$keys = explode('_', str_replace('.', '_', strtolower(implode('_', (array) $key))));
		
		$return_value = null;
		
		if(count($keys) > 0) {
			
			$test_keys = array();
			$test_keys[] = array_shift($keys);
			
			while(!array_key_exists(implode('_', $test_keys), $this->_data) && !empty($keys)) {
				$test_keys[] = array_shift($keys);
			}
			
			$key = implode('_', $test_keys);
			
			if(array_key_exists($key, $this->_data)) {
				
				$value = $this->_data[$key];
				
				if(count($keys) > 0 && is_object($value) && get_class($value) == 'ZPHP') {
					$return_value = $value->get_value($keys);
				} else {
					$return_value = is_null($value) ? $default : $value;
				}
			} 
			
		}

		return $this->_get_parsed_value($return_value);
	}

	
	public function has_value($name) {
		$value = $this->get_value($name);
		return !is_null($value);
	}
	
	//-------------------------------------------------------------------------------
	
	protected static function _update_mobile_detect()
	{
		if(is_null(self::$_IS_MOBILE) || is_null(self::$_IS_TABLET))
		{
			if(ZPHP::get_config('mobile_force'))
			{
				self::$_IS_MOBILE = true; 
				self::$_IS_TABLET = (bool) ZPHP::get_config('mobile_force_tablet'); 
			}
			else
			{
				$path = ZPHP::get_zframework_dir().'/core/thirdparty/mobile_detect/Mobile_Detect.php';
				@ require_once($path);
			
				$detect = new Mobile_Detect();
			
				self::$_IS_MOBILE = $detect->isMobile();
				self::$_IS_TABLET = $detect->isTablet();
			}
			
		}
		
	}
	
	public static function is_mobile()
	{
		self::_update_mobile_detect();
		return self::$_IS_MOBILE || self::$_IS_TABLET;
	}
		
	public static function is_tablet()
	{
		self::_update_mobile_detect();
		return self::$_IS_TABLET;
	}
	
}


/* Establecemos los reportes de errores por defecto -----------------------------------------------------------------*/

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING);

/* Controlamos si es que es una llamada cron ------------------------------------------------------------------------*/

if(defined('ZFRAMEWORK_CRON_CALL') && constant('ZFRAMEWORK_CRON_CALL'))
{
	$is_cron = true;
	$app_dir = $_SERVER['argv'][1];

	if(is_dir($app_dir))
	{
		$app_dir = realpath($app_dir);
		$cron_args_offset = 1;
	}
	else
	{
		$cron_args_offset = 0;
		$app_dir = ZPHP::get_app_dir();
	}

	ZPHP::load_initial_config($app_dir);
} 
else if(isset($_SERVER['argv']) && count($_SERVER['argv']) > 1 && $_SERVER['argv'][1] == ZPHP::get_cron_command()) {

	$is_cron = true;
	$app_dir = $_SERVER['argv'][2];

	if(is_dir($app_dir))
	{
		$cron_args_offset = 2;
		$app_dir = realpath($app_dir);
	}
	else
	{
		$cron_args_offset = 1;
		$app_dir = ZPHP::get_app_dir();
	}

	ZPHP::load_initial_config($app_dir);
}
else
{

	$is_cron = false;

	ZPHP::load_initial_config(ZPHP::get_app_dir());

}

/* Autocarga de clases -----------------------------------------------------------------*/
/* incluimos manualmente la clase para poder cargar clases */

@ include_once(dirname(__FILE__).'/core/services/classhelper.php');


spl_autoload_register(function($classname) {

	$path = ClassHelper::get_class_path($classname);

	if($path) {

		include_once($path);

	}

});


/* Cargamos la configuracion -----------------------------------------------------------------*/
/* Controlamos si es que es una llamada cron y ejecutamos (si es cron) -----------------*/

if($is_cron) {

	$cron_name = CLIHelper::get_arg($cron_args_offset);

	if($cron_name) {
		
		$cron = Cron::load_cron($cron_name, CLIHelper::get_args($cron_args_offset+1));
		
		$cron->run_cron();
		
	}
	
	exit;
}

unset($is_cron);

/* Iniciamos sesi�n -----------------------------------------------------*/

SessionHelper::init();

/* Incluimos los scripts al inicio -----------------------------------------------------*/
foreach(array_filter((array) ZPHP::get_config('auto_include_files')) as $path) {
	
	if(file_exists($path))
	{
		@ include_once($path);
	}
	
}

unset($path);

/* Funcion de error general ----- --------------------------------------------------------*/

function zphp_error_handler(Exception $ex) {

	try {		
		if(ZPHP::get_config('error_reporting_enabled')) {

			$error_html = new HTMLBlockErrorExceptionControl($ex);

			$email = new EmailHTML();
			$email->add_content($error_html->to_string());
			$email->set_subject('Error '.ZPHP::get_config('site_name'));

			foreach((array) ZPHP::get_config('error_reporting_recipients') as $email_address) {
				$email->add_to($email_address);
			}

			$email->send();
		}

	} catch(Exception $ex) {}	

	if(ZPHP::is_debug_mode() || ZPHP::is_development_mode()) {

		if(RedirectControl::is_ajax_call())
		{
			$error_block = new TextBlockErrorExceptionControl($ex);
			$error_block->out();
		}
		else
		{
			$error_page = new HTMLPageErrorException($ex);
			$error_page->out();
		}

		die();

	} else {

		NavigationHelper::location_error_not_found();

	}
}

/* Registro de la funci�n de shutdown --------------------------------------------------------*/

function zphp_shutdown_function() {

	if(($error = error_get_last()) && in_array($error['type'], array(E_ERROR, E_COMPILE_ERROR, E_CORE_ERROR, E_PARSE, E_STRICT))) {
		
		while(ob_get_level() > 0) {
			@ ob_end_clean();
		}

		$zexception = ZException::create_from_error($error);
		
		zphp_error_handler($zexception);
	}
	
}

register_shutdown_function('zphp_shutdown_function');

/* Si est� habilitado el soporte multi-idioma, lo inicializamos */

if(LanguageHelper::is_enabled()) {

	LanguageHelper::initialize();

}

/* Atajos de funciones */

if(!function_exists('text'))
{
	function text($key, $language=null) {

		return LanguageHelper::get_text($key, $language);
	}
}

if(!function_exists('url'))
{
	function url($id, $arg1=null, $arg2=null) {

		$args = func_get_args();
		return call_user_func_array(array('URLPattern', 'reverse'), $args);
	}
}

if(!function_exists('html'))
{
	function html($string) {

		return HTMLHelper::escape($string);
	}
}

if(!function_exists('html_quote'))
{
	function html_quote($string) {

		return HTMLHelper::quote($string);
	}
}

if(!function_exists('js'))
{
	function js($string) {

		return JSHelper::escape($string);
	}
}

if(!function_exists('js'))
{
	function js($string) {

		return JSHelper::escape($string);
	}
}

if(!function_exists('js_quote'))
{
	function js_quote($string) {

		return JSHelper::quote($string);
	}
}


if(!function_exists('url_html'))
{
	function url_html($id, $arg1=null, $arg2=null) {

		$args = func_get_args();
		$url = call_user_func_array(array('URLPattern', 'reverse'), $args);
		return HTMLHelper::escape($url);
	}
}



/* Set locale */
setlocale(LC_ALL, str_replace('-', '_', LanguageHelper::get_default_language()).'.'.strtoupper(ZPHP::get_config('charset')));
