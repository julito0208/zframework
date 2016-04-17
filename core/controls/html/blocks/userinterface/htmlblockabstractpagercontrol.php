<?php

abstract class HTMLBlockAbstractPagerControl extends HTMLControl {

	const DEFAULT_PAGE_VARNAME = 'p';
	const DEFAULT_LIMIT_VARNAME = 'pl';
	const DEFAULT_LIMIT = 10;
	const DEFAULT_BUTTONS_LIMIT = 5;
	
	/*--------------------------------------------------------------------------*/
	
	protected $_page = null;
	protected $_limit = null;
	protected $_total_results = null;
	protected $_page_varname = null;
	protected $_limit_varname = null;
	protected $_buttons_limit = null;
	protected $_final_pages;
	
	public function __construct($limit=self::DEFAULT_LIMIT, $page_varname=self::DEFAULT_PAGE_VARNAME, $limit_varname=self::DEFAULT_LIMIT_VARNAME, $buttons_limit=self::DEFAULT_BUTTONS_LIMIT) {
		parent::__construct();
		
		$this->set_page(0);
		$this->set_limit($limit);
		$this->set_buttons_limit($buttons_limit);
		$this->set_page_varname($page_varname);
		$this->set_limit_varname($limit_varname);
		$this->update_pager();
	}
	
	
	/*--------------------------------------------------------------------------*/
	
	abstract public function update_pager();
		
	abstract public function get_count_text();
	
	abstract public function generate_page_url($page);
	
	abstract public function generate_limit_url($limit);
	
	
	/*--------------------------------------------------------------------------*/
	
	public function get_page() {
		return $this->_page;
	}

	/* @return AbstractHTMLPager */
	public function set_page($value) {
		$this->_page = (int) $value;
		return $this;
	}

	public function get_limit() {
		return $this->_limit;
	}

	/* @return AbstractHTMLPager */
	public function set_limit($value) {
		$this->_limit = (int) $value;
		return $this;
	}
	
	
	public function get_buttons_limit() {
		return $this->_buttons_limit;
	}

	/* @return AbstractHTMLPager */
	public function set_buttons_limit($value) {
		$this->_buttons_limit = (int) $value;
		return $this;
	}


	public function get_limit_varname() {
		return $this->_limit_varname;
	}

	/* @return AbstractHTMLPager */
	public function set_limit_varname($value) {
		$this->_limit_varname = $value;
		return $this;
	}



	public function get_page_varname() {
		return $this->_page_varname;
	}

	/* @return AbstractHTMLPager */
	public function set_page_varname($value) {
		$this->_page_varname = $value;
		return $this;
	}

	/* @return AbstractHTMLPager */
	public function set_total_results($total) {
		$this->_total_results = (int) $total;
		return $this;
	}
	
	
	public function get_total_results() {
		return $this->_total_results;
	}
	
	
	public function get_count_pages() {
		return ceil($this->get_total_results()/$this->_limit);
	}

	public function get_offset() {
		return $this->_page * $this->_limit;
	}
	
	
	public function get_offset_limit() {
		return array('start' => $this->get_offset(), 'length' => $this->get_limit());
	}
	
	public function get_limit_array() {
		return array($this->get_offset(), $this->get_limit());
	}
	
	public function prepare_params() {

		$pages = $this->get_count_pages();
		$page_selected = $this->get_page();
		
		if($page_selected >= $pages) {
			$pages = max($page_selected+1, $pages);
		}
		
		$count_text = $this->get_count_text();
		
		if($this->get_buttons_limit() >= $pages) {
			
			$buttons_count = $pages;
			$page_start = 0;
			$first_button = false;
			$last_button = false;
			
		} else {
			
			$left_buttons_limit = floor($this->get_buttons_limit()/2);
			$right_buttons_limit = ceil($this->get_buttons_limit()/2);

			if($page_selected > $left_buttons_limit) {

				$page_start = $page_selected - $left_buttons_limit;
				$buttons_count = $pages - $page_start;
				$first_button = true;

			} else {

				$page_start = 0;
				$buttons_count = $pages;
				$first_button = false;

			}

			$last_button = false;
			
			if($pages-$page_selected > $right_buttons_limit) {
		
				$last_button = true;
				$buttons_count = $this->get_buttons_limit();
			}
			
			if($buttons_count < $this->get_buttons_limit()) {
				$page_start = $page_start - ($this->get_buttons_limit()-$buttons_count);
				$buttons_count = $buttons_count +  ($this->get_buttons_limit()-$buttons_count);
			}
		}
		
		
		$pages_urls = array();
		for($page_index=$page_start; $page_index<($page_start+$buttons_count);$page_index++)
			$pages_urls[] = $this->generate_page_url($page_index);

		$this->_final_pages = $pages;
		
		$this->set_param('page_selected', $page_selected);
		$this->set_param('pages', $pages);
		$this->set_param('page_start', $page_start);
		$this->set_param('buttons_count', $buttons_count);
		$this->set_param('total_results', $this->_total_results);
		$this->set_param('pages_urls', $pages_urls);
		$this->set_param('count_text', $count_text);
		$this->set_param('last_button', $last_button);
		$this->set_param('first_button', $first_button);
		$this->set_param('last_button_url', $last_button ? $this->generate_page_url($pages-1) : null);
		$this->set_param('first_button_url', $first_button ? $this->generate_page_url(0) : null);
		
		parent::prepare_params();
	}
	
}