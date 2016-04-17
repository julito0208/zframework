<?php //-----------------------------------------------------------------------


class HTMLInputRecaptchaControl extends HTMLInputControl {

	protected static function _get_sitekey()
	{
		return ZPHP::get_config('recaptcha.sitekey');
	}

	protected static function _get_privatekey()
	{
		return ZPHP::get_config('recaptcha.privatekey');
	}

	public static function check_captcha()
	{
		$request_data = array();
		$request_data['secret'] = self::_get_privatekey();
		$request_data['response'] = $_POST['g-recaptcha-response'];
		$request_data['remoteip'] = $_SERVER['REMOTE_ADDR'];

		$request = new URLRequest('https://www.google.com/recaptcha/api/siteverify', $request_data);
		$request->set_method('post');

		$response = $request->request_url();

		$json = JSONMap::parse($response);

		return $json->success;
	}

	//------------------------------------------------------------------------
	
	protected $_options = array();

	public function __construct() {

		parent::__construct();

		self::add_global_js_files('https://www.google.com/recaptcha/api.js');

		$this->set_option_sitekey(self::_get_sitekey());
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_sitekey($value)
	{
		$this->_options['sitekey'] = $value;
		return $this;
	}

	public function get_option_sitekey()
	{
		return $this->_options['sitekey'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_theme($value)
	{
		$this->_options['theme'] = $value;
		return $this;
	}

	public function get_option_theme()
	{
		return $this->_options['theme'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_type($value)
	{
		$this->_options['type'] = $value;
		return $this;
	}

	public function get_option_type()
	{
		return $this->_options['type'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_size($value)
	{
		$this->_options['size'] = $value;
		return $this;
	}

	public function get_option_size()
	{
		return $this->_options['size'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_tabindex($value)
	{
		$this->_options['tabindex'] = $value;
		return $this;
	}

	public function get_option_tabindex()
	{
		return $this->_options['tabindex'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_callback($value)
	{
		$this->_options['callback'] = $value;
		return $this;
	}

	public function get_option_callback()
	{
		return $this->_options['callback'];
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_option_expired_callback($value)
	{
		$this->_options['expired-callback'] = $value;
		return $this;
	}

	public function get_option_expired_callback()
	{
		return $this->_options['expired-callback'];
	}



	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('options', $this->_options);
	}
	
}

