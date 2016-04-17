<?php

class AjaxHTMLResponse extends HTML implements AjaxResponse
{
	public function __construct($html=null)
	{
		parent::__construct($html);
	}
}
