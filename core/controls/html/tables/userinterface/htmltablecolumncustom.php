<?php

class HTMLTableColumnCustom extends HTMLTableColumn
{

	public function set_title($title)
	{
		$this->_title = $title ? $title : '&nbsp;';
		return $this;
	}

	public function set_render_function($function)
	{
		$this->_render_function = $function ? $function : self::_get_default_row_render_function();
		return $this;
	}

	public function set_header_render_function($function)
	{
		$this->_header_render_function = $function ? $function : self::_get_default_header_render_function();
		return $this;
	}

	public function set_load_render_function($function)
	{
		$this->_load_render_function = $function ? $function : self::_get_default_load_render_function();
		return $this;
	}

}