<?php

class HTMLTable extends HTMLTag
{
	const MANTAIN_FILTERS_GET_VARNAME = 'tm';

	/*-------------------------------------------------------------*/

	public static function get_mantain_params()
	{
		return array(
			self::MANTAIN_FILTERS_GET_VARNAME => 1
		);
	}

	/*-------------------------------------------------------------*/

	protected static $_first_page_table = true;

	protected static function _parse_row($row)
	{
		if(is_array($row))
		{
			return new DictRead($row);
		}

		if(ClassHelper::is_instance_of($row, 'GetParam'))
		{
			return $row;
		}

	}

	/*-------------------------------------------------------------*/

	protected $_columns = array();
	protected $_rows = array();
	protected $_ordering = false;
	protected $_show_info = null;
	protected $_paging = true;
	protected $_empty_html;
	protected $_row_id_param = null;
	protected $_checked_rows = array();
	protected $_stateSave;
	protected $_reordering = false;
	protected $_reordering_callback = null;
	protected $_clear_filters = true;
	protected $_responsive = true;

	public function __construct($id=null)
	{
		parent::__construct();
		$this->_set_parse_all_parents_templates(false);
		$this->_set_table_id($id);
		self::add_global_static_library(self::STATIC_LIBRARY_DATA_TABLES);
		$this->set_empty_html(ZString::get('datatables_empty_table'));
	}


	public function __call($method, $args)
	{
		switch($method)
		{
			case 'get_num_columns':
				return $this->get_columns_count();
			break;

			case 'get_num_rows':
				return $this->get_rows_count();
			break;
		}
	}

	protected function _set_table_id($id=null)
	{
		if($id)
		{
			$this->_stateSave = true;
			$this->set_clear_filters(!$_GET[self::MANTAIN_FILTERS_GET_VARNAME]);

			$this->set_param('id', $id);
		}

		else if(self::$_first_page_table)
		{
			self::$_first_page_table = false;

			$this->set_clear_filters(!$_GET[self::MANTAIN_FILTERS_GET_VARNAME]);
			$this->_stateSave = true;

			$url = ZPHP::get_absolute_actual_uri();
			$url = strtolower($url);

			$id = 'table_'.preg_replace('#[^A-Za-z0-9]+#', '', $url);

			$this->set_param('id', $id);
		}
		else
		{
			$this->set_clear_filters(false);
			$this->_stateSave = false;

			$id = 'table_'.preg_replace('#[^A-Za-z0-9]+#', '', $url);

			$this->set_param('id', StringHelper::uniqid('table_'));
		}
	}


	public function get_row_id($row)
	{
		if(!is_object($row) && !is_array($row)) return $row;

		$row = self::_parse_row($row);

		if(ClassHelper::is_instance_of($row, 'OptionRow'))
		{
			return $row->get_row_id();
		}

		if(ClassHelper::is_instance_of($row, 'OptionItem'))
		{
			return $row->get_option_item_value();
		}

		return $row->get_param($this->get_row_id_param());
	}

	public function set_empty_html($value)
	{
		$this->_empty_html = $value;
		return $this;
	}

	public function get_empty_html()
	{
		return $this->_empty_html;
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return HTMLTableColumn
	*
	*/
	public function add_column($column_key, $title=null, $width=null, $render_function=null)
	{
		if(is_array($column_key))
		{
			foreach($column_key as $a)
			{
				$this->add_column($a);
			}

			return $column_key[0];
		}
		else if(ClassHelper::is_instance_of($column_key, 'HTMLTableColumn'))
		{
			$this->_columns[] = $column_key;
			return $column_key;
		}
		else if($column_key)
		{
			$column = new HTMLTableColumn($column_key, $title, $width, $render_function);
			$this->add_column($column);
			return $column;
		}
	}

	/**
	 *
	 * @return HTMLTableColumnNumeric
	 *
	 */
	public function add_column_numeric($key, $title=null, $width=HTMLTableColumnNumeric::DEFAULT_WIDTH, $decimals=HTMLTableColumnNumeric::DEFAULT_INTEGER_DECIMALS)
	{
		return $this->add_column(new HTMLTableColumnNumeric($key, $title, $width, $decimals));
	}


	/**
	 *
	 * @return HTMLTableColumnNumeric
	 *
	 */
	public function add_column_numeric_decimal($key, $title=null, $width=HTMLTableColumnNumeric::DEFAULT_WIDTH, $decimals=HTMLTableColumnNumeric::DEFAULT_DECIMAL_DECIMALS)
	{
		return $this->add_column_numeric($key, $title, $width, $decimals);
	}


	/**
	 *
	 * @return HTMLTableColumnCurrency
	 *
	 */
	public function add_column_currency($key, $title=null, $width=HTMLTableColumnCurrency::DEFAULT_WIDTH, $symbol=null, $decimals=null)
	{
		return $this->add_column(new HTMLTableColumnCurrency($key, $title, $width, $symbol, $decimals));
	}

	/**
	 *
	 * @return HTMLTableColumnDate
	 *
	 */
	public function add_column_date($key, $title=null, $width=HTMLTableColumnDate::DEFAULT_WIDTH, $include_hour=HTMLTableColumnDate::DEFAULT_INCLUDE_HOUR, $include_secs=HTMLTableColumnDate::DEFAULT_INCLUDE_SECS)
	{
		return $this->add_column(new HTMLTableColumnDate($key, $title, $width, $include_hour, $include_secs));
	}

	/**
	 *
	 * @return HTMLTableColumnDate
	 *
	 */
	public function add_column_datetime($key, $title=null, $width=HTMLTableColumnDate::DEFAULT_WIDTH, $include_secs=HTMLTableColumnDate::DEFAULT_INCLUDE_SECS)
	{
		return $this->add_column(new HTMLTableColumnDate($key, $title, $width, true, $include_secs));
	}

	/**
	 *
	 * @return HTMLTableColumnDate
	 *
	 */
	public function add_column_datetime_secs($key, $title=null, $width=HTMLTableColumnDate::DEFAULT_WIDTH)
	{
		return $this->add_column(new HTMLTableColumnDate($key, $title, $width, true, true));
	}

	/**
	 *
	 * @return HTMLTableColumn
	 *
	 */
	public function add_column_custom($title=null, $render_function=null, $column_key=null, $width=null)
	{
		return $this->add_custom_column($title, $render_function, $column_key, $width);
	}


	/**
	 *
	 * @return HTMLTableColumn
	 *
	 */
	public function add_column_custom_fixed($title=null, $width=null, $render_function=null, $column_key=null)
	{
		return $this->add_custom_column_fixed($title, $width, $render_function, $column_key);
	}

	/**
	 *
	 * @return HTMLTableColumnCheckbox
	 *
	 */
	public function add_column_checkbox()
	{
		return $this->add_column(new HTMLTableColumnCheckbox());
	}

	/**
	 *
	 * @return HTMLTableColumnImage
	 *
	 */
	public function add_column_image($thumb_type=null, $title='&nbsp;')
	{
		return $this->add_column(new HTMLTableColumnImage($thumb_type, $title));
	}

	/**
	*
	* @return HTMLTableColumnActions
	*
	*/
	public function add_column_action_checkbox($field, $title, $onclick=null, $filter_callback=null, $tag_callback=null)
	{
		$column = new HTMLTableColumnActions($title);
		$column->add_action_checkbox($field, $onclick, $filter_callback, $tag_callback);
		return $this->add_column($column);
	}


	/**
	 *
	 * @return HTMLTableColumn
	 *
	 */
	public function add_custom_column($title=null, $render_function=null, $column_key=null, $width=null)
	{

		if(!$column_key) $column_key = uniqid('column');

		return $this->add_column(new HTMLTableColumn($column_key, $title, $width, $render_function));
	}

	/**
	 *
	 * @return HTMLTableColumn
	 *
	 */
	public function add_custom_column_fixed($title=null, $width=null, $render_function=null, $column_key=null)
	{
		if(!$column_key) $column_key = uniqid('column');

		return $this->add_column(new HTMLTableColumn($column_key, $title, $width, $render_function));
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return HTMLTableColumn
	*
	*/

	public function get_column($column)
	{
		if($column && ClassHelper::is_instance_of($column, 'HTMLTableColumn'))
		{
			return $column;
		}
		else
		{
			foreach($this->_columns as $index => $c)
			{
				if($c->get_key() == $column || (is_numeric($column) && $index == $column))
				{
					return $c;
				}
			}
		}
	}

	public function add_row($row)
	{
		$row = self::_parse_row($row);
		$this->_rows[$this->get_row_id($row)] = $row;

		return $this;
	}

	public function add_rows(array $rows)
	{
		$args = func_get_args();
		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				foreach($arg as $a)
				{
					$this->add_row($a);
				}
			}

		}

		return $this;
	}

	public function set_ordering($value)
	{
		$this->_ordering = $value;
		return $this;
	}

	public function get_ordering()
	{
		return $this->_ordering;
	}

	public function set_show_info($value)
	{
		$this->_show_info = $value;
		return $this;
	}

	public function get_show_info()
	{
		return $this->_show_info;
	}

	public function set_paging($value)
	{
		$this->_paging = $value;
		return $this;
	}

	public function get_paging()
	{
		return $this->_paging;
	}
	
	public function set_row_id_param($value)
	{
		$this->_row_id_param = $value;
		return $this;
	}
	
	public function get_row_id_param()
	{
		return $this->_row_id_param;
	}

	public function get_row($id)
	{
		$id = $this->get_row_id($id);
		return $this->_rows[$id];
	}

	public function add_checked_rows($id)
	{
		$args = func_get_args();
		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				foreach($arg as $a)
				{
					$this->add_checked_rows($a);
				}
			}
			else
			{
				$this->_checked_rows[] = $this->get_row_id($arg);
			}
		}

		return $this;
	}

	public function remove_checked_rows($id)
	{
		$args = func_get_args();
		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				foreach($arg as $a)
				{
					$this->remove_checked_rows($a);
				}
			}
			else
			{
				$this->_checked_rows = array_diff($this->_checked_rows, array($this->get_row_id($arg)));
			}
		}

		return $this;
	}

	public function has_checked_row($id)
	{
		return in_array($this->get_row_id($arg), $this->_checked_rows);
	}

	public function clear_checked_rows()
	{
		$this->_checked_rows = array();
		return $this;
	}

	public function get_checked_rows()
	{
		return array_merge(array(), $this->_checked_rows);
	}

	public function call_js_checked_rows_function($field=null)
	{
		return $this->get_js_checked_rows_function().'('.JSHelper::cast($field).')';
	}

	public function get_js_checked_rows_function()
	{
		return '$("#'.$this->get_id().'").data("getCheckedRows")';
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_stateSave($value)
	{
		$this->_stateSave = $value;
		return $this;
	}

	public function get_stateSave()
	{
		return $this->_stateSave;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_reordering($value)
	{
		$this->_reordering = $value;
		return $this;
	}

	public function get_reordering()
	{
		return $this->_reordering;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_reordering_callback($value)
	{
		$this->_reordering_callback = $value;
		return $this;
	}

	public function get_reordering_callback()
	{
		return $this->_reordering_callback;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_clear_filters($value)
	{
		$this->_clear_filters = $value;
		return $this;
	}

	public function get_clear_filters()
	{
		return $this->_clear_filters;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_responsive($value)
	{
		$this->_responsive = $value;
		return $this;
	}

	public function get_responsive()
	{
		return $this->_responsive;
	}



	/*-------------------------------------------------------------*/

	public function get_rows_count()
	{
		return count($this->_rows);
	}

	public function get_columns_count()
	{
		return count($this->_columns);
	}


	/*-------------------------------------------------------------*/

	public function prepare_params()
	{
		parent::prepare_params();

		if($this->_reordering)
		{
			self::add_global_static_library(self::STATIC_LIBRARY_JQUERYUI);
			self::add_global_js_files(URLHelper::get_zframework_static_url('thirdparty/datatables/extensions/RowReordering/dataTables.rowReordering.js'));
		}

		$prepared_rows = array();
		$prepared_columns = array();
		$array_rows = array();
		$has_checkbox_column = false;

		foreach($this->_rows as $row)
		{
			$prepared_row = array();

			foreach($this->_columns as $column)
			{
				$prepared_row[] = $column->call_row_render_function($row, $this);
			}

			$prepared_rows[] = $prepared_row;
			$array_rows[$this->get_row_id($row)] = $row->get_params_array();
		}

		foreach($this->_columns as $column)
		{
			$prepared_column = array();
			$prepared_column['title'] = $column->call_header_render_function($this);
			$prepared_column['orderable'] = $column->get_orderable();
			$prepared_column['class'] = trim($column->get_class());

			$prepared_columns[] = $prepared_column;

			if(ClassHelper::is_instance_of($column, 'HTMLTableColumnCheckbox'))
			{
				$has_checkbox_column = true;
			}
		}

		$this->set_param('columns', $this->_columns);
		$this->set_param('rows', $this->_rows);
		$this->set_param('prepared_rows', $prepared_rows);
		$this->set_param('prepared_columns', $prepared_columns);
		$this->set_param('show_info', is_null($this->_show_info) ? ($this->_paging && count($this->_rows) > 10) : $this->_show_info);
		$this->set_param('paging', $this->_paging);
		$this->set_param('ordering', $this->_ordering);
		$this->set_param('empty_html', $this->_empty_html);
		$this->set_param('checked_rows', $this->_checked_rows);
		$this->set_param('has_checkbox_column', $has_checkbox_column);
		$this->set_param('array_rows', $array_rows);
		$this->set_param('stateSave', $this->_stateSave);
		$this->set_param('reordering', $this->_reordering);
		$this->set_param('reordering_callback', $this->_reordering_callback);
		$this->set_param('clear_filters', $this->_clear_filters);
		$this->set_param('responsive', $this->_responsive); 
	}
}