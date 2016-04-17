<?php

class HTML extends HTMLControl
{
	public function __construct($html=null)
	{
		parent::__construct();
		$this->set_content($html);
	}
}
