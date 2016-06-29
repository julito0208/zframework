<?php

class HTMLTableColumnNumeric extends HTMLTableColumn
{
	const DEFAULT_CLASS = 'numeric-column numeric';
	const DEFAULT_WIDTH = 130;
	const DEFAULT_INTEGER_DECIMALS = 0;
	const DEFAULT_DECIMAL_DECIMALS = 2;

	protected $_decimals;

	public function __construct($key, $title=null, $width=self::DEFAULT_WIDTH, $decimals=self::DEFAULT_INTEGER_DECIMALS)
	{
		parent::__construct($key, $title, $width);
		$this->add_class(self::DEFAULT_CLASS);
		$this->_render_function = array($this, '_render_function_callback');
		$this->set_decimals($decimals);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_decimals($value)
	{
		$this->_decimals = $value;
		return $this;
	}

	public function get_decimals()
	{
		return $this->_decimals;
	}



	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column)
	{
		$key = $this->_key;
		$tag = new HTMLLongTag('div');
		$tag->set_content(NumbersHelper::decimal_format($row->$key, $this->_decimals));
		return $tag->to_string();
	}
}