<?php

class HTMLTableColumnActions extends HTMLTableColumn
{
	protected static $_row_json_pattern = "#\\{\\{row\\}\\}#";
	protected static $_row_field_pattern = "#\\{\\{row\\:(?P<field>\\w+)\\}\\}#";

	protected static $_tag_replace_expressions = array(
		'checked' => array(
			"#\\{\\{checked\\}\\}#",
			'$(this).checked()',
		),
		'id' => array(
			"#\\{\\{id\\}\\}#",
			'{{row:__id__}}',
		),
	);

	/*-------------------------------------------------------------*/

	const ROW_ID_FIELD = '__id__';
	const DEFAULT_CLASS = 'action-column action';

	/*-------------------------------------------------------------*/

	protected $_actions_tags = array();

	public function __construct($title='&nbsp;')
	{
		parent::__construct(StringHelper::uniqid('actions_'), $title, 1);
		$this->add_style('text-align', 'center');
		$this->_render_function = array($this, '_render_function_callback');
	}

	/*-------------------------------------------------------------*/

	protected static function _replace_tag_expressions($tag_html)
	{
		foreach(self::$_tag_replace_expressions as $expression_name => $pattern_replace)
		{
			list($pattern, $replace) = $pattern_replace;
			$tag_html = preg_replace($pattern, $replace, $tag_html);
		}

		return $tag_html;
	}

	protected function _parse_onclick_function($onclick)
	{
		if(strpos($onclick, '!') === 0)
		{
			$control = substr($onclick, 1);

			if(strpos($onclick, '(') !== false)
			{
				list($control, $param) = explode('(', $control);
				$param = str_replace(')', '', $param);

				$onclick = "Navigation.go('".URLPattern::reverse($control, $param)."');";
			}
			else
			{
				$onclick = "Navigation.go('".URLPattern::reverse($control, '{{row:__id__}}')."');";
			}

		}

		if(strpos($onclick, '(') === false)
		{
			return $onclick.'({{row}})';
		}
		else
		{
			return $onclick;
		}
	}

	protected function _set_tag_onclick_function(HTMLTag $tag, $onclick)
	{
		if($onclick)
		{
			$tag->set_param('onclick', $this->_parse_onclick_function($onclick));
		}
	}

	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column, HTMLTable $table)
	{
		$array = $row->get_params_array();
		$json = HTMLHelper::escape(JSHelper::cast_obj($array));

		$block = new HTMLLongTag('div');
		$block->add_style('white-space', 'nowrap');
		$block->add_style('text-align', 'center');
		$block->add_style('padding', '0 10px');

		$html = "";

		foreach($column->_actions_tags as $index => $array_parts)
		{
			if($index > 0)
			{
				$html.= '&nbsp; &nbsp;';
			}

			list($tag, $filter_callback, $tag_callback, $column_callback) = $array_parts;

			$tag = clone $tag;

			if($column_callback)
			{
				if(call_user_func_array($column_callback, array($tag, $row, $column, $table)) === false)
				{
					continue;
				}
			}

			if($filter_callback && !call_user_func_array($filter_callback, array($row, $column, $table)))
			{
				continue;
			}

			if($tag_callback)
			{
				call_user_func_array($tag_callback, array($tag, $row, $column, $table));
			}

			$tag_html = $tag->to_string();

			$tag_html = self::_replace_tag_expressions($tag_html);


			if(preg_match(self::$_row_json_pattern, $tag_html, $match))
			{
				$tag_html = preg_replace(self::$_row_json_pattern, $json, $tag_html);
			}

			if(preg_match_all(self::$_row_field_pattern, $tag_html, $matches))
			{
				foreach($matches['0'] as $index => $match)
				{
					$field_name = $matches['field'][$index];

					if($field_name == self::ROW_ID_FIELD)
					{
						$field = $table->get_row_id($row);
					}
					else
					{
						$field = $row->get_param($field_name);
					}

					$tag_html = str_replace($match, $field, $tag_html);
				}

			}



			$html.= $tag_html;
		}

		$block->set_html($html);

		return $block;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	protected function _add_action_tag(HTMLTag $tag, $onclick=null, $filter_callback=null, $tag_callback=null, $column_callback=null)
	{
		$tag->add_class('table-action-link');
		$this->_set_tag_onclick_function($tag, $onclick);
		$this->_actions_tags[] = array($tag, $filter_callback, $tag_callback, $column_callback);
		return $this;
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function add_action_tag(HTMLTag $tag, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$this->_add_action_tag($tag, $onclick, $filter_callback, $tag_callback);
		return $this;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function add_action_checkbox($field, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$tag = new HTMLShortTag('input');
		$tag->set_param('type', 'checkbox');

		$column_callback = function(HTMLTag $tag, GetParam $row) use ($field) {

			$tag->set_param('value', '{{row:'.HTMLTableColumnActions::ROW_ID_FIELD.'}}');
			$tag->set_param('id', 'checkbox-'.$field.'-{{row:'.HTMLTableColumnActions::ROW_ID_FIELD.'}}');
			$tag->add_class('checkbox-'.$field);

			if($row->get_param($field))
			{
				$tag->set_param('checked', 'checked');
			}
		};

		return $this->_add_action_tag($tag, $onclick, $filter_callback, $tag_callback, $column_callback);
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function add_action_link_text_icon($title, $icon_class, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='icon {$icon_class}'> </span><span class='text'>{$title}</span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->_add_action_tag($tag, $onclick, $filter_callback, $tag_callback);
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function add_action_link_text($title, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='text'>{$title}</span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->_add_action_tag($tag, $onclick, $filter_callback, $tag_callback);
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function add_action_link_icon($title, $icon_class, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='icon {$icon_class}'> </span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->_add_action_tag($tag, $onclick, $filter_callback, $tag_callback);
	}

	public function get_class()
	{
		$this->add_class(self::DEFAULT_CLASS);
		return parent::get_class();
	}

	public function prepare_params()
	{
		$this->add_class(self::DEFAULT_CLASS);
		parent::prepare_params();

	}

	/*
	protected static $_row_json_pattern = "#\\{\\{row\\}\\}#";
	protected static $_row_field_pattern = "#\\{\\{row\\:(?P<field>\\w+)\\}\\}#";

	const ROW_ID_FIELD = '__id__';
	const DEFAULT_CLASS = 'action-column action';

	protected $_actions_tags = array();

	public function __construct($title='&nbsp;')
	{
		parent::__construct(StringHelper::uniqid('actions_'), $title, 1);
		$this->add_style('text-align', 'center');
		$this->_render_function = array($this, '_render_function_callback');
	}

	protected function _parse_onclick_function($onclick, $field=null)
	{
		if(strpos($onclick, '!') === 0)
		{
			$control = substr($onclick, 1);

			if(strpos($onclick, '(') !== false)
			{
				list($control, $param) = explode('(', $control);
				$param = str_replace(')', '', $param);

				$onclick = "Navigation.go('".URLPattern::reverse($control, $param)."');";
			}
			else
			{
				$onclick = "Navigation.go('".URLPattern::reverse($control, '{{row:__id__}}')."');";
			}

		}

		if(strpos($onclick, '(') === false)
		{
			if($field)
			{
				return $onclick.'({{row:'.$field.'}})';
			}
			else
			{
				return $onclick.'({{row}})';
			}
		}
		else
		{
			return $onclick;
		}
	}

	protected function _set_tag_onclick_function(HTMLTag $tag, $onclick, $field=null)
	{
		if($onclick)
		{
			$tag->set_param('onclick', $this->_parse_onclick_function($onclick, $field));
		}
	}

	protected function _render_function_callback(GetParam $row, HTMLTableColumn $column, HTMLTable $table)
	{
		$array = $row->get_params_array();
		$json = HTMLHelper::escape(JSHelper::cast_obj($array));

		$block = new HTMLLongTag('div');
		$block->add_style('white-space', 'nowrap');
		$block->add_style('text-align', 'center');
		$block->add_style('padding', '0 10px');

		$html = "";

		foreach($column->_actions_tags as $index => $array_parts)
		{
			if($index > 0)
			{
				$html.= '&nbsp; &nbsp;';
			}

			list($tag, $filter_callback) = $array_parts;

			if($filter_callback && !call_user_func_array($filter_callback, array($row, $column, $table)))
			{
				continue;
			}

			$tag_html = $tag->to_string();

			if(preg_match(self::$_row_json_pattern, $tag_html, $match))
			{
				$tag_html = preg_replace(self::$_row_json_pattern, $json, $tag_html);
			}

			if(preg_match(self::$_row_field_pattern, $tag_html, $match))
			{
				if($match['field'] == self::ROW_ID_FIELD)
				{
					$field = $table->get_row_id($row);
				}
				else
				{
					$field = $row->get_param($match['field']);
				}

				$tag_html = preg_replace(self::$_row_field_pattern, JSHelper::cast($field), $tag_html);
			}

			$html.= $tag_html;
		}

		$block->set_html($html);

		return $block;
	}

	public function add_action_tag(HTMLTag $tag, $onclick=null, $filter_callback=null, $field=null)
	{
		$tag->add_class('table-action-link');
		$this->_set_tag_onclick_function($tag, $onclick, $field);
		$this->_actions_tags[] = array($tag, $filter_callback);
		return $this;
	}

	public function add_action_checkbox($onclick=null, $filter_callback=null, $field=null)
	{
		$tag = new HTMLShortTag('input');
		$tag->set_param('type', 'checkbox');
		return $this->add_action_tag($tag, $onclick, $filter_callback, $field);
	}

	public function add_action_link_text_icon($title, $icon_class, $onclick=null, $filter_callback=null, $field=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='icon {$icon_class}'> </span><span class='text'>{$title}</span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->add_action_tag($tag, $onclick, $filter_callback, $field);
	}

	public function add_action_link_text($title, $onclick=null, $filter_callback=null, $field=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='text'>{$title}</span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->add_action_tag($tag, $onclick, $filter_callback, $field);
	}

	public function add_action_link_icon($title, $icon_class, $onclick=null, $filter_callback=null, $field=null)
	{
		$tag = new HTMLLongTag('a');
		$tag->set_content("<span class='icon {$icon_class}'> </span>");
		$tag->set_param('title', $title);
		$tag->set_param('href', 'javascript: void(0)');
		return $this->add_action_tag($tag, $onclick, $filter_callback, $field);
	}

	public function get_class()
	{
		$this->add_class(self::DEFAULT_CLASS);
		return parent::get_class();
	}

	public function prepare_params()
	{
		$this->add_class(self::DEFAULT_CLASS);
		parent::prepare_params();

	}
	*/
}