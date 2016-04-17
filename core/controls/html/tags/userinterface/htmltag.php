<?php

abstract class HTMLTag extends HTMLControl
{

	public static function _prepare_style_name($name)
	{
		return trim(strtolower($name));
	}

	public static function _style_html($style)
	{
		$style_array = array();

		foreach($style as $name => $value)
		{
			if($value)
			{
				$style_array[] = "{$name}: {$value}";
			}
		}

		return implode('; ', $style_array);
	}

	protected static function _get_classes($class)
	{
		return explode(' ', preg_replace('#\s+#', ' ', trim($class)));
	}

	protected $_tagname;
	protected $_show_content = true;

	public function __construct($tagname='')
	{
		parent::__construct();
		$this->_set_tagname($tagname);
	}

	protected function _set_show_content($value)
	{
		$this->_show_content = $value;
	}

	protected function _get_style()
	{
		$style = array();
		$style_str = $this->get_param('style');

		if(!$style_str)
		{
			return $style;
		}

		foreach(explode(';', $style_str) as $style_part)
		{
			$style_parts = explode(':', trim($style_part), 2);

			if(count($style_parts) > 1)
			{
				$name = self::_prepare_style_name($style_parts[0]);

				if($name)
				{
					$style[strtolower($style_parts[0])] = $style_parts[1];
				}
			}
		}

		return $style;
	}

	protected function _get_attr_html()
	{
		$params = $this->_original_params;

		$html = "";

		foreach($params as $key => $value)
		{
			$value = trim($value);

			if($value)
			{
				$html.= " {$key}=\"".HTMLHelper::escape($value)."\"";
			}
		}

		return $html;
	}

	protected function _set_tagname($value)
	{
		$this->_tagname = $value;
		return $this;
	}

	protected function _get_tagname()
	{
		return $this->_tagname;
	}

	public function has_class($classname)
	{
		$classes = self::_get_classes($this->get_param('class'));
		return in_array($classname, $classes);
	}

	public function add_class($classname)
	{
		$classes = self::_get_classes($this->get_param('class'));
		$args = func_get_args();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				foreach($arg as $a)
				{
					$this->add_class($a);
				}

				$classes = self::_get_classes($this->get_param('class'));
			}
			else
			{
				foreach(self::_get_classes($arg) as $a) {

					if (!in_array($a, $classes)) {
						$classes[] = $a;
						$this->set_param('class', implode(' ', $classes));
					}
				}
			}
		}

		return $this;
	}

	public function remove_class($classname)
	{
		$classes = self::_get_classes($this->get_param('class'));
		$args = func_get_args();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				foreach($arg as $a)
				{
					$this->remove_class($a);
				}

				$classes = self::_get_classes($this->get_param('class'));
			}
			else
			{
				foreach(self::_get_classes($arg) as $a) {
					if (in_array($a, $classes)) {
						$classes = array_diff($classes, array($a));
						$this->set_param('class', implode(' ', $classes));
					}
				}
			}
		}

		return $this;
	}

	public function has_style($name)
	{
		$name = self::_prepare_style_name($name);
		$style = $this->_get_style();

		return array_key_exists($name, $style);
	}

	public function add_style($name, $value=null, $overwrite=true)
	{
		if(is_array($name))
		{
			foreach($name as $n => $v)
			{
				$this->add_style($n, $v, $overwrite);
			}

			return $this;
		}

		$name = self::_prepare_style_name($name);
		$style = $this->_get_style();

		if(is_null($value))
		{
			if(array_key_exists($name, $style))
			{
				unset($style[$name]);
			}
		}
		else if($overwrite || !array_key_exists($name, $style))
		{
			$style[$name] = $value;
		}

		$this->set_param('style', self::_style_html($style));
		return $this;
	}

	public function add_default_style($name, $value=null)
	{
		return $this->add_style($name, $value, false);
	}

	public function remove_style($name)
	{
		if(is_array($name))
		{
			foreach($name as $n)
			{
				$this->remove_style($n);
			}

			return $this;
		}

		$name = self::_prepare_style_name($name);
		$style = $this->_get_style();

		if(array_key_exists($name, $style))
		{
			unset($style[$name]);
			$this->set_param('style', self::_style_html($style));
		}

		return $this;
	}

	public function get_style($name=null)
	{
		$style = $this->_get_style();

		if(func_num_args() == 0)
		{
			return $style;
		}
		else
		{
			$name = self::_prepare_style_name($name);
			return $style[$name];
		}
	}

	public function get_class()
	{
		return trim($this->get_param('class'));
	}

	public function set_id($id)
	{
		return $this->set_param('id', $id);
	}

	public function get_id()
	{
		return $this->get_param('id');
	}

	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('tagname', $this->_tagname);
		$this->set_param('attr_html', $this->_get_attr_html());
		$this->set_param('show_content', $this->_show_content);
	}
}