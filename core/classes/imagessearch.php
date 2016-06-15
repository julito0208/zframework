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

	public static function search_images($search)
	{
		$urls = array();

		$args = func_get_args();
		$args = ArrayHelper::plain($args);

		foreach($args as $arg)
		{
			$search = new YahooImagesSearch();
			$urls = array_merge($urls, $search->search($arg));
		}

		return (array) $urls;
	}

	/*-------------------------------------------------------------*/

	protected $_searchs = array();

	public function __construct($search=null)
	{
		$this->add_search($search);
	}

	/*-------------------------------------------------------------*/

	protected function _search_images($search)
	{
		return array();
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

	public function search($search=null)
	{
		$args = func_get_args();

		if(!empty($args))
		{
			$searchs = $this->_searchs;
			$this->_searchs = array();
			$this->add_search($args);
			$results = $this->search();
			$this->_searchs = $searchs;
			return $results;
		}
		else
		{
			$urls = array();

			foreach($this->_searchs as $search)
			{
				if(get_class($this) == 'ImagesSearch')
				{
					$urls = array_merge($urls, self::search_images($search));
				}
				else
				{
					$urls = array_merge($urls, $this->_search_images($search));
				}

			}

			return (array) $urls;
		}

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