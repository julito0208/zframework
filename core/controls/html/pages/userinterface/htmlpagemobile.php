<?php 


class HTMLPageMobile extends HTMLPage {

	public function __construct($params=null)
	{
		parent::__construct($params);
		self::add_global_static_library('HTMLControlStaticLibraryJQueryMobile');
	}
	
}