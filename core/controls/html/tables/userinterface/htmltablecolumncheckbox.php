<?php

class HTMLTableColumnCheckbox extends HTMLTableColumn
{
	const DEFAULT_CLASS = 'checkbox-column checkbox';

	protected $_name = null;
	protected $_show_header_checkbox = true;

	public function __construct()
	{
		parent::__construct(StringHelper::uniqid('checkbox_'), '&nbsp;', 1);
		$this->add_style('text-align', 'center');
		$this->_render_function = array($this, '_row_render_function');
		$this->_header_render_function = array($this, '_header_render_function');
		$this->_load_render_function = array($this, '_load_render_function');
	}

	protected function _get_row_id_html($id, HTMLTable $table)
	{
		$name = $this->_name ? $this->_name : $table->get_id();
		return $name.'_'.$id;
	}

	protected function _load_render_function(HTMLTableColumn $column, HTMLTable $table)
	{
		$checked_rows = $table->get_checked_rows();
		$js = '';

		foreach($checked_rows as $id)
		{
			$id = $this->_get_row_id_html($id, $table);
			$js.= '$("#'.HTMLHelper::escape($id).'").click(); ';
		}

		return $js;

	}

	protected function _header_render_function(HTMLTableColumn $column, HTMLTable $table)
	{
		if($this->_show_header_checkbox)
		{
			$html = '<input type="checkbox" ';
			$html.= 'class="checkbox column-checkbox" ';
			$html.= 'value="1" ';
			$html.= 'id="1" ';
			$html.= 'onclick="if($(this).is(\':checked\')) $(this).getParent(\'table\').find(\'tbody tr td input.checkbox\').not(\':checked\').trigger(\'click\'); else $(this).getParent(\'table\').find(\'tbody tr td input.checkbox:checked\').trigger(\'click\');" ';
			$html.= ' />';

			return $html;
		}

		return $this->get_title();

	}

	protected function _row_render_function(GetParam $row, HTMLTableColumn $column, HTMLTable $table)
	{
		$value = $table->get_row_id($row);
		$name = $this->_name ? $this->_name : $table->get_id();

		$html = '<input type="checkbox" ';
		$html.= 'class="checkbox column-checkbox" ';
		$html.= 'value="'.HTMLHelper::escape($value).'" ';
		$html.= 'id="'.HTMLHelper::escape($this->_get_row_id_html($value, $table)).'" ';
		$html.= 'name="'.HTMLHelper::escape($name.'[]').'" ';
		$html.= 'onclick="$(this).getParent(\'tr\').toggleClass(\'checked\', $(this).attr(\'checked\'));" ';
		$html.= ' />';

		return $html;
	}

	public function set_name($value)
	{
		$this->_name = $value;
		return $this;
	}
	
	public function get_name()
	{
		return $this->_name;
	}
	
	public function set_show_header_checkbox($value)
	{
		$this->_show_header_checkbox = $value;
		return $this;
	}

	public function get_show_header_checkbox()
	{
		return $this->_show_header_checkbox;
	}



	public function prepare_params()
	{
		$this->add_class(self::DEFAULT_CLASS);
		parent::prepare_params();

	}

}