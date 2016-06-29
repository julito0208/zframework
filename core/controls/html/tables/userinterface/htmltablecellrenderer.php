<?php

class HTMLTableCellRenderer extends HTMLControl
{
	/**
	*
	* @var GetParam
	*
	*/
	protected $_row;

	/**
	*
	* @var HTMLTableColumn
	*
	*/
	protected $_column;

	/**
	*
	* @var HTMLTable
	*
	*/
	protected $_table;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function render_cell(GetParam $row, HTMLTableColumn $column=null, HTMLTable $table=null)
	{
		$this->_row = $row;
		$this->_column = $column;
		$this->_table = $table;
		return $this->to_string();
	}

	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('row', $this->_row);
		$this->set_param('table', $this->_table);
		$this->set_param('column', $this->_column); 
	}

}