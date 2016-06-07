<?php

abstract class ImagesSearch
{
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

	abstract protected function _search_images($search);

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
				$urls = array_merge($urls, $this->_search_images($search));
			}

			return (array) $urls;
		}

	}
}