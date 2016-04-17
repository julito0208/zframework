<?php

class AjaxJSONResponse extends JSONMap implements AjaxResponse
{
	public function __construct($data=array()) {
		parent::__construct($data);
	}


}