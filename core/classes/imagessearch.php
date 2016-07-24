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

	protected static $_void_urls = array();

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
		if(is_null($search))
		{
			$search = $_REQUEST['search'];
		}

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