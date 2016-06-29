<?php

class HTMLBlockDebugData extends HTMLControl
{

	public static function ajax_get_resource_size()
	{
		$json = new AjaxJSONResponse();

		$url = $_POST['url'];
		$size = URLRequest::get_url_size($url);

		$json->set_item('size', $size);
		$json->out();
	}

	public static function ajax_get_resources_sizes()
	{
		$json = new AjaxJSONResponse();

		$urls = (array) $_POST['urls'];
		$sizes = array();
		$total = 0;

		foreach($urls as $url)
		{
			$size = URLRequest::get_url_size($url);

			if($size <= 0) continue;

			$size_formatted = NumbersHelper::number_format_size($size, 2);

			$sizes[] = array(
				'url' => $url,
				'size' => $size,
				'size_formatted' => $size_formatted,
			);

			$total+= $size;
		}

		usort($sizes, function($a, $b) {

			if($a['size'] > $b['size'])
			{
				return -1;
			}
			else if($a['size'] < $b['size'])
			{
				return 1;
			}
			else
			{
				return 0;
			}

		});

		$json->set_item('sizes', $sizes);
		$json->set_item('total', $total);
		$json->set_item('total_formatted', NumbersHelper::number_format_size($total, 2));
		$json->out();
	}


	/*-------------------------------------------------------------*/

	protected $_debug_data;
	protected $_content_len;
	
	public function __construct($debug_data, $content_len=null)
	{
		parent::__construct();	
		$this->_debug_data = $debug_data;
		$this->_content_len = $content_len;


	}
	
	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('debug_data', $this->_debug_data);
		$this->set_param('content_len', $this->_content_len);
	}
}