<?php

interface GetParam
{
	public function get_param($name, $default=null);

	public function get_params_array();

	public function __toArray();
}