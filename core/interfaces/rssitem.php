<?php //----------------------------------------------------------------------


interface RSSItem {
	
	public function rss_item_title();
	
	public function rss_item_link();
	
	public function rss_item_description();
	
	public function rss_item_autor();
	
	public function rss_item_guid();
	
	public function rss_item_pubdate();
}


//--------------------------------------------------------------------------- ?>