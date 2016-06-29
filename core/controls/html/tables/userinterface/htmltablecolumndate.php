<?php

class HTMLTableColumnDate extends HTMLTableColumn
{
	const DEFAULT_CLASS = 'date-column date';
	const DEFAULT_WIDTH = 160;
	const DEFAULT_INCLUDE_HOUR = false;
	const DEFAULT_INCLUDE_SECS = false;

	protected $_include_hour;
	protected $_include_secs;

	public function __construct($key, $title=null, $width=self::DEFAULT_WIDTH, $include_hour=self::DEFAULT_INCLUDE_HOUR, $include_secs=self::DEFAULT_INCLUDE_SECS)
	{
		parent::__construct($key, $title, $width);
		$this->add_class(self::DEFAULT_CLASS);
		$this->_render_function = array($this, '_render_function_callback');
		$this->set_include_hour($include_hour);
		$this->set_include_secs($include_secs);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_include_hour($value)
	{
		$this->_include_hour = $value;
		return $this;
	}

	public function get_include_hour()
	{
		return $this->_include_hour;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_include_secs($value)
	{
		$this->_include_secs = $value;
		return $this;
	}

	public function get_include_secs()
	{
		return $this->_include_secs;
	}



	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column)
	{
		$key = $this->_key;
		$value = $row->$key;
		$date = Date::parse($value);

		if(!$this->_include_hour)
		{
			$string = $date->format_default_date();
		}
		else if($this->_include_hour && !$this->_include_secs)
		{
			$string = $date->format_default_datetime(false);
		}
		else
		{
			$string = $date->format_default_datetime(true);
		}

		$tag = new HTMLLongTag('div');
		$tag->set_content($string);
		return $tag->to_string();
	}
}