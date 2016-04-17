<?php

class HTMLTableColumnCurrency extends HTMLTableColumn
{
	const DEFAULT_CLASS = 'currency-column numeric-column numeric';
	const DEFAULT_WIDTH = 130;

	protected $_decimals;
	protected $_symbol;

	public function __construct($key, $title=null, $width=self::DEFAULT_WIDTH, $symbol=null, $decimals=null)
	{
		parent::__construct($key, $title, $width);
		$this->add_class(self::DEFAULT_CLASS);
		$this->_render_function = array($this, '_render_function_callback');
		$this->set_decimals($decimals);
		$this->set_decimals($symbol);
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

	/**
	*
	* @return $this
	*
	*/
	public function set_symbol($value)
	{
		$this->_symbol = $value;
		return $this;
	}

	public function get_symbol()
	{
		return $this->_symbol;
	}



	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column)
	{
		$key = $this->_key;
		$tag = new HTMLLongTag('div');
		$tag->set_content(NumbersHelper::currency_format($row->$key, $this->_symbol, $this->_decimals));
		return $tag->to_string();
	}
}