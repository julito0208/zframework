<?php

class HTMLBlockDefaultPager extends HTMLBlockAbstractPagerControl {

	protected $_parsed = false;
	
	
	public function __construct($limit=self::DEFAULT_LIMIT, $page_varname=self::DEFAULT_PAGE_VARNAME, $limit_varname=self::DEFAULT_LIMIT_VARNAME, $buttons_limit=self::DEFAULT_BUTTONS_LIMIT) {
		parent::__construct($limit, $page_varname, $limit_varname, $buttons_limit);
		HTMLControl::add_global_js_files_zframework('/js/controls/pagerlist.js');
	}

	public function update_pager() {
		if(HTTPGet::exists($this->get_page_varname())) 
			$this->set_page(HTTPGet::get_item_int ($this->get_page_varname()));
		
		if(HTTPGet::exists($this->get_limit_varname())) 
			$this->set_limit(HTTPGet::get_item_int ($this->get_limit_varname()));
	}
	
	
	public function get_count_text() {
		$init = $this->get_page() * $this->get_limit();
		if($init == 0) $init = 1;
		$end = min(($this->get_page() + 1 ) * $this->get_limit(), $this->get_total_results());
		return String::get('showing')." <span class='number init-offset'>{$init}</span><span class='separator'>-</span><span class='number end-offset'>{$end}</span> <span class='separator'>".String::get('of')."</span> <span class='number total'>".$this->get_total_results()."</span>";
		
	}
	
	
	public function generate_page_url($page) {
		return NavigationHelper::make_url_query(array($this->get_page_varname() => $page));
	}
	
	public function generate_limit_url($limit) {
		return NavigationHelper::make_url_query(array($this->get_limit_varname() => $limit));
	}
	
	
	
	public function prepare_params() {
		parent::prepare_params();
	}

	
	public function out() {
		
		if(!$this->_parsed || ($this->_parsed && $this->_final_pages > 1)) {
			$this->_parsed = true;
			return parent::out();
		}
		
	}
}