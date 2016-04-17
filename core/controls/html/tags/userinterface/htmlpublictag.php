<?php

abstract class HTMLPublicTag extends HTMLTag
{
	public function __construct($tagname)
	{
		parent::__construct($tagname);

	}

	public function set_tagname($value)
	{
		$this->_tagname = $value;
		return $this;
	}

	public function get_tagname()
	{
		return $this->_tagname;
	}


}