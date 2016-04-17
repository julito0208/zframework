<?php

class HTMLShortTag extends HTMLPublicTag
{
	public function __construct($tagname)
	{
		parent::__construct($tagname);
		$this->_set_show_content(false);
	}
}