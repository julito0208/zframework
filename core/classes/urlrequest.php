<?php 

class URLRequest extends Dict {

	public static function get_url_size($url, $method=null, $data=array())
	{
		$request = new URLRequest($url, $method, $data);
		$request->request_headers();
		return $request->get_header_content_length();
	}

	/*-------------------------------------------------------------*/

	const METHOD_POST = 'post';
	const METHOD_GET = 'get';

	const DEFAULT_METHOD = 'get';

	const HEADER_URL = 'url';
	const HEADER_CONTENT_TYPE = 'content_type';
	const HEADER_HTTP_CODE = 'http_code';
	const HEADER_HEADER_SIZE = 'header_size';
	const HEADER_SIZE_DOWNLOAD = 'size_download';
	const HEADER_DOWNLOAD_CONTENT_LENGTH = 'download_content_length';
	const HEADER_CONTENT_LENGTH = self::HEADER_DOWNLOAD_CONTENT_LENGTH;

	/*-------------------------------------------------------------*/

	protected $_method;
	protected $_url;
	protected $_read_only_headers = false;
	protected $_response_headers = array();
	protected $_void_ssl_certificate = true;

	public function __construct($url, $method=self::DEFAULT_METHOD, $data=array())
	{
		parent::__construct();
		$this->set_url($url);
		$this->set_method($method);
		$this->update($data);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_method($value)
	{
		$this->_method = strtolower($value) == self::METHOD_POST ? self::METHOD_POST : self::METHOD_GET;
		return $this;
	}

	public function get_method()
	{
		return $this->_method;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_url($value)
	{
		$this->_url = NavigationHelper::conv_abs_url($value);
		return $this;
	}

	public function get_url()
	{
		return $this->_url;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_read_only_headers($value)
	{
		$this->_read_only_headers = $value;
		return $this;
	}

	public function get_read_only_headers()
	{
		return $this->_read_only_headers;
	}

	/**
	 * @return $this
	 */
	protected function set_void_ssl_certificate($value)
	{
		$this->_void_ssl_certificate = $value;
		return $this;
	}

	protected function get_void_ssl_certificate()
	{
		return $this->_void_ssl_certificate;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_method($value)
	{
		$this->_method = $value;
		return $this;
	}

	public function get_method()
	{
		return $this->_method;
	}




	/*-------------------------------------------------------------*/

	public function request($read_body=null)
	{
		if(is_null($read_body) || func_num_args() == 0)
		{
			$read_body = !$this->get_read_only_headers();
		}

		$params = $this->get_array();
		$url = $this->_url;

		if($this->get_method() == self::METHOD_POST)
		{
			$curl_post = true;
			$curl_post_params = $params;
		}
		else
		{
			$curl_post = false;
			$curl_post_params = array();
			$url = NavigationHelper::make_url_query($params, $url);
		}

		$curl = curl_init();
		$curl_options = array(
			CURLOPT_URL => $url,
			CURLOPT_POST => $curl_post,
			CURLOPT_POSTFIELDS => $curl_post_params,
			CURLOPT_NOBODY => !$read_body,
		);

		if($this->_void_ssl_certificate)
		{
			$curl_options[CURLOPT_SSL_VERIFYPEER] = false;
		}

		curl_setopt_array($curl, $curl_options);

		if($read_body)
		{
			ob_start();
			curl_exec($curl);
			$response = ob_get_clean();
		}
		else
		{
			$response = curl_exec($curl);
		}

		$this->_response_headers = curl_getinfo($curl);

		curl_close($curl);

		return $response;
	}

	public function request_headers()
	{
		return $this->request(false);
	}

	/*-------------------------------------------------------------*/

	public function get_header($key=null)
	{
		if(is_null($key) || func_num_args() == 0)
		{
			return array_merge(array(), $this->_response_headers);
		}
		else
		{
			return $this->_response_headers[$key];
		}
	}

	public function get_headers()
	{
		return array_merge(array(), $this->_response_headers);
	}

	/*-------------------------------------------------------------*/

	public function get_header_url()
	{
		return $this->get_header(self::HEADER_URL);
	}

	public function get_header_content_type()
	{
		return $this->get_header(self::HEADER_CONTENT_TYPE);
	}

	public function get_header_http_code()
	{
		return $this->get_header(self::HEADER_HTTP_CODE);
	}

	public function get_header_size()
	{
		return $this->get_header(self::HEADER_HEADER_SIZE);
	}

	public function get_header_size_download()
	{
		return $this->get_header(self::HEADER_SIZE_DOWNLOAD);
	}

	public function get_header_download_content_length()
	{
		return $this->get_header(self::HEADER_DOWNLOAD_CONTENT_LENGTH);
	}


	public function get_header_content_length()
	{
		return $this->get_header(self::HEADER_CONTENT_LENGTH);
	}


}

