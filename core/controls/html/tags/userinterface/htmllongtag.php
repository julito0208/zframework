<?php

class HTMLLongTag extends HTMLPublicTag
{
	public function __construct($tagname)
	{
		parent::__construct($tagname);
		$this->_set_show_content(true);
	}
}