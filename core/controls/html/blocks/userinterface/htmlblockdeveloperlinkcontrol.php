<?php

class HTMLBlockDeveloperLinkControl extends HTMLControl
{

	const BUTTON_IMAGE_URL = "/zframework/static/images/developer_boton.png";
	const BUTTON_HREF = "http://www.zentefi.com.ar";
	const BUTTON_TITLE = "Zentefi";
	
	public function __construct() {
		parent::__construct();
	}


	public function prepare_params() {
		parent::prepare_params();
		$this->set_param('button_image_url', self::BUTTON_IMAGE_URL);
		$this->set_param('button_href', self::BUTTON_HREF);
		$this->set_param('button_title', self::BUTTON_TITLE);
	}
}