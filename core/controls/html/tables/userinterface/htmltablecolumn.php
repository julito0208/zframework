<?php

class HTMLTableColumn extends HTMLTag
{

	private static $_default_header_tagname = 'th';
	private static $_default_row_tagname = 'td';
	private static $_default_render_function = null;
	private static $_default_header_render_function = null;
	private static $_default_load_render_function = null;

	protected static function _get_default_row_render_function()
	{
		if(is_null(self::$_default_render_function))
		{
			self::$_default_render_function = create_function('GetParam $row, HTMLTableColumn $column, HTMLTable $table', '$key = $column->get_key(); return $row->$key;');
		}

		return self::$_default_render_function;
	}

	protected static function _get_default_header_render_function()
	{
		if(is_null(self::$_default_header_render_function))
		{
			self::$_default_header_render_function = create_function('HTMLTableColumn $column, HTMLTable $table', 'return $column->get_title();');
		}

		return self::$_default_header_render_function;
	}


	protected static function _get_default_load_render_function()
	{
		if(is_null(self::$_default_load_render_function))
		{
			self::$_default_load_render_function = create_function('HTMLTableColumn $column, HTMLTable $table', 'return "";');
		}

		return self::$_default_load_render_function;
	}


	protected $_key;
	protected $_title;
	protected $_render_function;
	protected $_header_render_function;
	protected $_load_render_function;
	protected $_row_tag;
	protected $_orderable = false;

	public function __construct($key, $title=null, $width=null, $render_function=null)
	{
		parent::__construct(self::$_default_header_tagname);
		$this->_set_show_content(true);
		$this->set_key($key);
		$this->set_width($width);

		$this->_title = $title ? $title : '&nbsp;';
		$this->_render_function = $render_function ? $render_function : self::_get_default_row_render_function();
		$this->_load_render_function = self::_get_default_load_render_function();
		$this->_header_render_function = self::_get_default_header_render_function();

		$this->_row_tag = new HTMLLongTag(self::$_default_row_tagname);
	}

	public function set_key($value)
	{
		$this->_key = $value;
		return $this;
	}

	public function get_key()
	{
		return $this->_key;
	}

	public function get_title()
	{
		return $this->_title;
	}

	public function get_render_function()
	{
		return $this->_render_function;
	}

	public function get_header_render_function()
	{
		return $this->_header_render_function;
	}

	public function get_load_render_function()
	{
		return $this->_load_render_function;
	}

	public function set_width($value)
	{
		$this->add_style('width', $value && is_numeric($value) ? "{$value}px" : $value);
		return $this;
	}

	public function get_width()
	{
		return $this->get_style('width');
	}

	public function set_orderable($value)
	{
		$this->_orderable = $value;
		return $this;
	}

	public function get_orderable()
	{
		return $this->_orderable;
	}

	public function get_class()
	{
		$this->add_class($this->get_key().' column-'.$this->get_key());
		return parent::get_class();
	}

	public function call_row_render_function($row, HTMLTable $table)
	{
		if(is_object($this->_render_function) && $this->_render_function instanceof HTMLTableCellRenderer)
		{
			return $this->_render_function->render_cell($row, $this, $table);
		}
		else
		{
			return (string) call_user_func($this->_render_function, $row, $this, $table);
		}
	}

	public function call_header_render_function(HTMLTable $table)
	{
		return (string) call_user_func($this->_header_render_function, $this, $table);
	}

	public function call_load_render_function(HTMLTable $table)
	{
		return (string) call_user_func($this->_load_render_function, $this, $table);
	}

	public function prepare_params()
	{
		$this->add_class($this->get_key().' column-'.$this->get_key());
		parent::prepare_params();

	}

}