<?php 

class HTMLPageDevelopToolIndexPage extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '(?:/|(?:/index(?:\.php))?)?';
			
	protected $_urls;
	
	public function __construct() {
	
		parent::__construct();

		$this->_urls = array();
		
		foreach(self::get_tools_urls() as $url_pattern)
		{
			if($url_pattern->get_id() != get_class())
			{
				eval("\$title = {$url_pattern->get_id()}::_get_title();");
				eval("\$show_index = {$url_pattern->get_id()}::_get_show_index();");
				
				if($show_index)
				{
					$this->_urls[] = array('url_pattern' => $url_pattern, 'title' => $title);
				}
				
			}
			
		}
		
		$this->_show_back = false;
		
	}
	
	public function prepare_params() {
			
		parent::prepare_params();
		$this->set_param('urls', $this->_urls);
		
	}
}
