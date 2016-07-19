<?php

class ImagesSearch implements MIMEControl
{
	public static function get_url_pattern() {
		$url_format = ZPHP::get_config('image_search_url');
		$url_pattern = preg_quote($url_format);
		$url_pattern = str_replace(preg_quote('%s'), '(.*)', $url_pattern);
		return new URLPattern($url_pattern, 'ImagesSearch', 'ImagesSearch');
	}

	//---------------------------------------------------------------------------------------------

	protected static $_void_urls = array(
		'https://tse2.mm.bing.net/th?id=OIP.M446d527f7f420fbc3c3507a4548beedbo0&pid=15.1&P=0&w=600&h=600',
		'https://tse1.mm.bing.net/th?id=OIP.M5d33d30b04a6ede5a5120927c200596aH0&pid=15.1&P=0&w=600&h=600',
		'https://tse4.mm.bing.net/th?id=OIP.M3fec9810d94785e46354517a8f9d2e4bH0&pid=15.1&P=0&w=600&h=600',
		'https://tse3.mm.bing.net/th?id=OIP.M1ea263c99fbf094ecfca9701f9a6618aH0&pid=15.1&P=0&w=600&h=600',
		'https://tse1.mm.bing.net/th?id=OIP.Mc9f3815b5b98d6f53cc67a3a46bc9590H0&pid=15.1&P=0&w=600&h=600',
		'https://tse3.mm.bing.net/th?id=OIP.Md56bbfff4721974d7dbd3b7fe7e0b30dH0&pid=15.1&P=0&w=600&h=600',
		'https://tse3.mm.bing.net/th?id=OIP.M54704ac5965e2164afc8ee7d7af01797o0&pid=15.1&P=0&w=600&h=600',
		'https://tse1.mm.bing.net/th?id=OIP.M317f54ae47a4c2e9f40df057c75f9b6eH0&pid=15.1&P=0&w=600&h=600',
		'https://tse2.mm.bing.net/th?id=OIP.M4cff58337d725d83d2a84fed95a914cfo0&pid=15.1&P=0&w=600&h=600',
		'https://tse2.mm.bing.net/th?id=OIP.M4cff58337d725d83d2a84fed95a914cfo0&pid=15.1&P=0&w=600&h=600',
		'https://tse1.mm.bing.net/th?id=OIP.M213705b30b969eba273b72d619122faao0&pid=15.1&P=0&w=600&h=600',
		'https://tse4.mm.bing.net/th?id=OIP.M65e86627d9ab78ab182be83ed43732e4H0&pid=15.1&P=0&w=600&h=600',
		'https://tse3.mm.bing.net/th?id=OIP.M9b3c141f85e6a4501170af2a60d9264eo0&pid=15.1&P=0&w=600&h=600',
		'https://tse3.mm.bing.net/th?id=OIP.Mf21031287c65999c353a85c8ab74559fH0&pid=15.1&P=0&w=600&h=600',
	);

	public static function search_images($search, $pages=null)
	{
		set_time_limit(0);

		$urls = array();

		$pages = $pages ? $pages : ZPHP::get_config('image.search_pages');

		$engine = new YahooImagesSearch();
		$urls = array_merge($urls, $engine->search($search, $pages));

		$urls = array_diff((array) $urls, self::$_void_urls);

		return $urls;
	}

	/*-------------------------------------------------------------*/

	protected $_searchs = array();
	protected $_pages = 0;

	public function __construct($search=null, $pages=null)
	{
		$this->add_search($search);

		if(is_null($pages))
		{
			if(isset($_REQUEST['pages']))
			{
				$pages = $_REQUEST['pages'];
			}
			else
			{
				$pages = ZPHP::get_config('image.search_pages');
			}
		}

		$this->set_pages($pages);
	}

	/*-------------------------------------------------------------*/

	protected function _search_images($search, $pages)
	{
		return array();
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function set_pages($value)
	{
		$this->_pages = $value;
		return $this;
	}

	public function get_pages()
	{
		return $this->_pages;
	}



	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function add_search($search)
	{
		$args = func_get_args();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				call_user_func_array(array($this, 'add_search'), $arg);
			}
			else
			{
				$this->_searchs[] = $arg;
			}
		}

		return $this;
	}

	public function search($search=null, $pages=null)
	{
		if(is_null($search))
		{
			$search = $this->_searchs;
		}

		if(is_null($pages))
		{
			$pages = $this->_pages;
		}

		if(get_class($this) == 'ImagesSearch')
		{
			$urls = self::search_images($search, $pages);
		}
		else
		{
			$urls = $this->_search_images($search, $pages);
		}

		return (array) $urls;

	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return JSONMap
	*
	*/
	public function get_json()
	{
		$json = new JSONMap();
		$urls = $this->search();
		$json->set_item('urls', $urls);
		return $json;
	}

	public function out()
	{
		@ header('Content-Type: application/json; charset="iso-8859-15');
		$this->get_json()->out();
	}

	public function out_attachment($attachment_filename = null)
	{
		$this->get_json()->out_attachment($attachment_filename);
	}

	public function save_to($filename)
	{
		$this->get_json()->save_to($filename);
	}
}