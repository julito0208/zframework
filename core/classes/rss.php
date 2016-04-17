<?php 

class RSS implements MIMEControl {

	private static $_default_version = '2.0';
	private static $_default_link = '/';
	private static $_default_language = 'es-ar';
	private static $_default_namespaces = "xsi='http://www.w3.org/2001/XMLSchema-instance'";
	
	//----------------------------------------------------------------------
	
	protected static function _prepare_item_attr_name($name) {
		return strtolower(preg_replace('/[^A-Za-z0-9]+/', '', $name));
	}
	
	
	protected static function _format_date($str) {
		if(!$str) return null;
		else if(!is_numeric($str)) return $str;
		else if($str instanceof Date) return $str->get_total_seconds();
		else return date('r', $str);
	}
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------

	public static function escape($string) { return XML::escape($string); }
		
	
	public static function quote($string){ return XML::quote($string); }
		
	
	public static function unescape($string){ return XML::unescape($string); }
	
	//----------------------------------------------------------------------
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public static function parse($str, $load_default=false) {
		$xml = XML::parse($str);
		
		if(!$xml || strtolower($xml->get_tagname()) != 'rss') return null;
		else {
			
			$attrs = array('namespaces'=>array());
			if(($xml_version = $xml->get_version())) $attrs['xml_version'] = $xml_version;
			if(($charset = $xml->get_charset())) $attrs['charset'] = $charset;
			if(($version = $xml->get_attr('version'))) $attrs['version'] = $version;
			
			foreach($xml->get_attr() as $name=>$value)
				if(strpos($name, 'xmlns:')===0)
					$attrs['namespaces'][$name] = $value;

					
			$channel_node = $xml->get_node('channel');
			
			if($channel_node) {
				
				foreach(array(
					'title'=>'title',
					'link'=>'link',
					'description'=>'description',
					'lastBuildDate'=>'last_build_date',
					'pubDate'=>'pub_date',
					'language'=>'language',
					'webMaster'=>'web_master',
					'managingEditor'=>'editor',
					'category'=>'category',
					'generator'=>'generator',
					'ttl'=>'life_time') as $node_name => $attr_name)
					
					if(($node=$channel_node->get_node($node_name))) 
						$attrs[$attr_name] = $node->get_text();
					
						
					if(($node=$channel_node->find_node('image:url')))
						$attrs['image'] = $node->get_text();
				
						
					$attrs['items']	= $channel_node->get_nodes('item');
			}
					
		
		
			$rss = new RSS($attrs, $load_default);
			return $rss;
		}
	}
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public static function load($filename, $load_default=false) {
		$str = @ file_get_contents($filename);
		$rss = self::parse($str, $load_default);
		if($rss) $rss->set_file($filename);
		return $rss;
	}
	
		
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	protected $_link;
	protected $_title;
	protected $_description;
	protected $_language;
	protected $_last_build_date;	
	protected $_version;
	protected $_image;
	protected $_pub_date;
	protected $_web_master;
	protected $_category;
	protected $_editor;
	protected $_life_time;
	protected $_generator;
	protected $_base_url = ZPHP_RSS_BASE_URL;
	protected $_items = array();
	protected $_namespaces = array();
	
	/**
	 * @var XML
	 */	
	protected $_xml;
	
	public function __construct($attrs=array()){
		
		$this->_xml = XML::create('rss');

		$this->set_attr('xml_version', '1.0');
		$this->set_attr('charset', ZPHP::get_config('charset'));
		$this->set_attr('mimetype', 'text/xml');
		$this->set_attr('version', self::$_default_version);
		$this->set_attr('link', self::$_default_link);
		$this->set_attr('language', self::$_default_language);
		$this->set_attr('title', ZPHP::get_config('site_name'));
		$this->set_attr('namespaces', self::$_default_namespaces);
		$this->set_attr('base_url', ZPHP::get_config('site_url'));
				
		$this->set_attr($attrs);		
		
	}
	
	
	public function __toString() { return $this->to_string(); }
		
	
	public function __toArray() { return $this->get_items(); }
	
	
	public function __set($name, $value=null) { return $this->set_attr($name, $value); }
	
	
	public function __get($name) { return $this->get_attr($name); }
	
	
	//----------------------------------------------------------------------
		
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_attr($arg1, $arg2=null){
		if(!is_array($arg1)) $attrs = array($arg1=>$arg2);
		else $attrs = $arg1;
		
		foreach($attrs as $key=>$value)
			switch(trim(strtolower(($key)))) {

				case 'title': $this->set_title($value); break;
				case 'description': $this->set_description($value); break;
				case 'link': $this->set_link($value); break;
				case 'build_date': case 'last_build_date': $this->set_build_date($value); break;
				case 'version': $this->set_version($value); break;
				case 'language': $this->set_language($value); break;
				case 'namespaces': $this->set_namespaces($value); break;
				case 'mimetype': $this->set_mimetype($value); break;
				case 'charset': case 'encoding': $this->set_charset($value); break;
				case 'file': $this->set_file($value); break;
				case 'xml_version': $this->set_xml_version($value); break;
				case 'styles': case 'style_files': $this->set_style_files($value); break;
				case 'image': $this->set_image($value); break;
				case 'pub_date': case 'pubdate': $this->set_pub_date($value); break;
				case 'web_master': $this->set_web_master($value); break;
				case 'editor': $this->set_editor($value); break;
				case 'generator': $this->set_generator($value); break;
				case 'life_time': $this->set_life_time($value); break;
				case 'category': $this->set_category($value); break;
				case 'base_url': $this->set_base_url($value); break;
				case 'items': $this->set_items_array($value); break;
			}	
		
		return $this;
	}
	
	
	public function get_attr($key){
		switch(trim(strtolower(($key)))) {

			case 'title': return $this->get_title(); break;
			case 'description': return $this->get_description(); break;
			case 'link': return $this->get_link(); break;
			case 'build_date': case 'last_build_date': return $this->get_build_date(); break;
			case 'version': return $this->get_version(); break;
			case 'language': return $this->get_language(); break;
			case 'namespaces': return $this->get_namespaces(); break;
			case 'mimetype': return $this->get_mimetype(); break;
			case 'charset': case 'encoding': return $this->get_charset(); break;
			case 'file': return $this->get_file(); break;
			case 'xml_version': return $this->get_xml_version(); break;
			case 'styles': case 'style_files': return $this->get_style_files(); break;
			case 'image': return $this->get_image(); break;
			case 'pub_date': case 'pubdate': return $this->get_pub_date(); break;
			case 'web_master': return $this->get_web_master(); break;
			case 'editor': return $this->get_editor(); break;
			case 'generator': return $this->get_generator(); break;
			case 'life_time': return $this->get_life_time(); break;
			case 'category': return $this->get_category(); break;
			case 'base_url': return $this->get_base_url(); break;
			case 'items': return$this->get_items(); break;
		}	
	
		return $this;
	}
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function attr($arg1, $arg2=null){
		$args = func_get_args();
		$num_args = count($args); 
		if($num_args == 1 && !is_array($arg1)) return call_user_func_array(array($this,'get_attr'), $args);
		else return call_user_func_array(array($this,'set_attr'), $args);
	}
	
	
	//------------------------------------------------------------------------------------
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_title($title=null){
		$this->_title = trim($title);
		return $this;
	}
	
	
	public function get_title() { return $this->_title; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function title($title=null){
		if(func_num_args() > 0) return $this->set_title($title);
		else return $this->get_title();
	}
	
	
	
	
			
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_version($version=null){
		$this->_version = trim($version);
		return $this;
	}
	
	
	public function get_version() { return $this->_version; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function version($version=null){
		if(func_num_args() > 0) return $this->set_version($version);
		else return $this->get_version();
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_link($link=null){
		$this->_link = trim($link);
		return $this;
	}
	
	
	public function get_link() { return $this->_link; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function link($link=null){
		if(func_num_args() > 0) return $this->set_link($link);
		else return $this->get_link();
	}
	
	
	
	
			
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_description($description=null){
		$this->_description = trim($description);
		return $this;
	}
	
	
	public function get_description() { return $this->_description; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function description($description=null){
		if(func_num_args() > 0) return $this->set_description($description);
		else return $this->get_description();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_language($language=null){
		$this->_language = trim($language);
		return $this;
	}
	
	
	public function get_language() { return $this->_language; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function language($language=null){
		if(func_num_args() > 0) return $this->set_language($language);
		else return $this->get_language();
	}
	
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_image($image=null){
		$this->_image = trim($image);
		return $this;
	}
	
	
	public function get_image() { return $this->_image; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function image($image=null){
		if(func_num_args() > 0) return $this->set_image($image);
		else return $this->get_image();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_build_date($build_date=null){
		$this->_build_date = trim($build_date);
		return $this;
	}
	
	
	public function get_build_date() { return $this->_build_date; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function build_date($build_date=null){
		if(func_num_args() > 0) return $this->set_build_date($build_date);
		else return $this->get_build_date();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_pub_date($pub_date=null){
		$this->_pub_date = trim($pub_date);
		return $this;
	}
	
	
	public function get_pub_date() { return $this->_pub_date; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function pub_date($pub_date=null){
		if(func_num_args() > 0) return $this->set_pub_date($pub_date);
		else return $this->get_pub_date();
	}
	
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_web_master($web_master=null){
		$this->_web_master = trim($web_master);
		return $this;
	}
	
	
	public function get_web_master() { return $this->_web_master; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function web_master($web_master=null){
		if(func_num_args() > 0) return $this->set_web_master($web_master);
		else return $this->get_web_master();
	}
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_editor($editor=null){
		$this->_editor = trim($editor);
		return $this;
	}
	
	
	public function get_editor() { return $this->_editor; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function editor($editor=null){
		if(func_num_args() > 0) return $this->set_editor($editor);
		else return $this->get_editor();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_life_time($life_time=null){
		$this->_life_time = trim($life_time);
		return $this;
	}
	
	
	public function get_life_time() { return $this->_life_time; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function life_time($life_time=null){
		if(func_num_args() > 0) return $this->set_life_time($life_time);
		else return $this->get_life_time();
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_generator($generator=null){
		$this->_generator = trim($generator);
		return $this;
	}
	
	
	public function get_generator() { return $this->_generator; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function generator($generator=null){
		if(func_num_args() > 0) return $this->set_generator($generator);
		else return $this->get_generator();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_category($category=null){
		$this->_category = trim($category);
		return $this;
	}
	
	
	public function get_category() { return $this->_category; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function category($category=null){
		if(func_num_args() > 0) return $this->set_category($category);
		else return $this->get_category();
	}
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_base_url($base_url=null){
		$this->_base_url = trim($base_url);
		return $this;
	}
	
	
	public function get_base_url() { return $this->_base_url; }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function base_url($base_url=null){
		if(func_num_args() > 0) return $this->set_base_url($base_url);
		else return $this->get_base_url();
	}
	
	
	//----------------------------------------------------------------------
		
	/**
	 * 
	 * @return RSS
	 */
	public function add_namespace($arg1, $arg2=null){
		if(func_num_args() == 2) $attrs = array($arg1=>$arg2);
		else if(!is_array($arg1)) $attrs = HTMLHelper::parse_attrs($arg1);
		else $attrs = $arg1;
		
		foreach($attrs as $key=>$value) {
			$key = preg_replace('/^(?i)xmlns\:/','',strtolower(trim($key)));
			if(is_null($value)) unset($this->_namespaces[$key]);
			else $this->_namespaces[$key] = $value;
		}
								
		return $this;
	}
	
	
	public function get_namespace($name=null){
		if(func_num_args() > 0) return $this->_namespaces[preg_replace('/^(?i)xmlns\:/','',strtolower(trim($name)))];
		else return $this->_namespaces;
	}
	
	
	public function get_namespaces(){
		return $this->_namespaces;
	}
	
	
	public function has_namespace($name) {
		return array_key_exists(preg_replace('/^(?i)xmlns\:/','',strtolower(trim($name))), $this->_namespaces);
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function remove_namespace($name=null) {
		foreach(func_get_args() as $name)
			unset($this->_namespaces[preg_replace('/^(?i)xmlns\:/','',strtolower(trim($name)))]);
			
		return $this;
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function clear_namespaces() {
		$this->_namespaces = array();
		return $this;
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_namespaces($arg1, $arg2=null) {
		$this->clear_namespaces();
		$args = func_get_args();
		return call_user_func_array(array($this,'add_namespace'), $args);
	}
	//----------------------------------------------------------------------
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_mimetype($mimetype=null){
		$this->_xml->set_mimetype($mimetype);
		return $this;
	}
	
	
	public function get_mimetype() { return $this->_xml->get_mimetype(); }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function mimetype($mimetype=null){
		if(func_num_args() > 0) return $this->set_mimetype($mimetype);
		else return $this->get_mimetype();
	}
	
	
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_charset($charset=null){
		$this->_xml->set_charset($charset);
		return $this;
	}
	
	
	public function get_charset() { return $this->_xml->get_charset(); }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function charset($charset=null){
		if(func_num_args() > 0) return $this->set_charset($charset);
		else return $this->get_charset();
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_encoding($encoding=null) { return $this->set_charset($encoding); }
	
	
	public function get_encoding() { return $this->get_charset(); }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function encoding($encoding=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'charset'), $args);
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_xml_version($version=null){
		$this->_xml->set_version($version);
		return $this;
	}
	
	
	public function get_xml_version() { return $this->_xml->get_version(); }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function xml_version($version=null){
		if(func_num_args() > 0) return $this->set_xml_version($version);
		else return $this->get_xml_version();
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_file($file=null){
		$this->_xml->set_file($file);
		return $this;
	}
	
	
	public function get_file() { return $this->_xml->get_file(); }
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function file($file=null){
		if(func_num_args() > 0) return $this->set_file($file);
		else return $this->get_file();
	}
	
	
	//------------------------------------------------------------------------------------
		
	/**
	 * 
	 * @return RSS
	 */
	public function add_style_files($files, $media=null, $type=null){
		$args = func_get_args();
		call_user_func_array(array($this->_xml, 'add_style_files'), $args);
		return $this;
	}
	
	
	
	public function has_style_file($file) { 
		$args = func_get_args();
		return call_user_func_array(array($this->_xml, 'has_style_file'), $args);
		
	}
	
	
	
	public function get_style_file_media($file) { 
		$args = func_get_args();
		return call_user_func_array(array($this->_xml, 'get_style_file_media'), $args);
	}
	
	
	
	public function get_style_file_type($file) { 
		$args = func_get_args();
		return call_user_func_array(array($this->_xml, 'get_style_file_type'), $args);
	}
	
	
	public function get_style_files($get_info=false) { 
		$args = func_get_args();
		return call_user_func_array(array($this->_xml, 'get_style_files'), $args);
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function remove_style_files($file){
		$args = func_get_args();
		call_user_func_array(array($this->_xml, 'remove_style_files'), $args);
		return $this;
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function clear_style_files(){
		$this->_xml->clear_style_files();
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_style_files($file, $media=null, $type=null){
		$args = func_get_args();
		call_user_func_array(array($this->_xml, 'set_style_files'), $args);
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function style_files($arg=null){
		if(func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this, 'add_style_files'), $args);
		} else return $this->get_style_files();
	}
	
	
	public function count_style_files() { return $this->_xml->count_style_files(); }
	

	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
		
	/**
	 * 
	 * @return RSS
	 */
	public function append_items($item=null) {
		
		foreach(func_get_args() as $arg) {
			
			if($arg instanceof XML) {
								
				if($arg->get_tagname()=='item') {
		
					
					$item = array();
					if(($node = $arg->get_node('title'))) $item['title'] = trim($node->get_text());
					if(($node = $arg->get_node('link'))) $item['link'] = trim($node->get_text());
					if(($node = $arg->get_node('description'))) $item['description'] = trim($node->get_text());
					if(($node = $arg->get_node('autor'))) $item['autor'] = trim($node->get_text());
					if(($node = $arg->get_node('category'))) $item['category'] = trim($node->get_text());
					if(($node = $arg->get_node('guid'))) $item['guid'] = trim($node->get_text());
					if(($node = $arg->get_node('pubDate'))) $item['pubdate'] = trim($node->get_text());
					$this->_items[] = array_filter($item);
					
				} else if($arg->get_tagname()=='rss') $this->append_items($arg->get_node('channel'));
				
				else call_user_func_array(array($this, 'append_items'), $arg->get_nodes('item'));
				
			} else if(($rss_item_array_instance = ($arg instanceof RSSItemArray)) || ($rss_item_instance = ($arg instanceof RSSItem)) || (is_array($arg) || (is_object($arg) && method_exists($arg, '__toArray')))) {

				if($rss_item_instance) {
				
					$item = array();
					$item['title'] = $arg->rss_item_title();
					$item['link'] = $arg->rss_item_link();
					$item['description'] = $arg->rss_item_description();
					$item['autor'] = $arg->rss_item_autor();
					$item['guid'] = $arg->rss_item_guid();					
					$item['pubdate'] = $arg->rss_item_pubdate();	
					
				} else if($rss_item_array_instance) $item = (array) $arg->to_rss_item();
				
				else $item = CastHelper::to_array($arg);
				
				foreach($item as $name=>$value) $item[self::_prepare_item_attr_name($name)] = trim($value);
				
				$item = ArrayHelper::reduce_keys($item, 'title', 'link', 'description', 'autor', 'pubdate', 'category', 'guid');
				
				$this->_items[] = array_filter($item);
				
			} else if($arg) call_user_func_array(array($this, 'append_items'), XML::parse_nodes_array($arg));
			
		}
		
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function append_items_array($items=array()) {
		return call_user_func_array(array($this, 'append_items'), is_array($items) ? $items : array($items));
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function prepend_items($item=null) {
		$old_items = $this->_items;
		$this->_items = array();
		$args = func_get_args();
		call_user_func_array(array($this, 'append_items'), $args);
		$this->_items = array_merge($this->_items, $old_items);
		return $this;
	}
	
	/**
	 * 
	 * @return RSS
	 */
	public function prepend_items_array($items=array()) {
		return call_user_func_array(array($this, 'prepend_items'), is_array($items) ? $items : array($items));
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function clear_items(){
		$this->_items = array();
		return $this;
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_items($item=null){
		$this->clear_items();
		$args = func_get_args();
		return call_user_func_array(array($this, 'append_items'), $args);
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function set_items_array($items=array()) {
		return call_user_func_array(array($this, 'set_items'), is_array($items) ? $items : array($items));
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function remove_items($index=null) {
		foreach(func_get_args() as $indexs)
			foreach((array) $indexs as $index)
				unset($this->_items[$index]);
				
		$this->_items = array_values($this->_items);
		return $this;
	}
	
	
	/**
	 * 
	 * @return RSS
	 */
	public function remove_item($index=null) {
		$args = func_get_args();
		return call_user_func_array(array($this, 'remove_items'), $args);
	}
	
	
	public function count_items() { return count($this->_items); }
	
	
	public function get_items($indexs=null, $keys=null){
		if(is_null($indexs)) $indexs = range(0, $this->count_items()-1);
		$items = array_values(ArrayHelper::reduce_keys($this->_items, (array) $indexs));
		
		if(!is_null($keys)) {
			
			if(is_array($keys)) {
				$keys = array_map(array(self,'_prepare_item_attr_name'), $keys);
				foreach($items as $index => $item)
					$items[$index] = ArrayHelper::reduce_keys($item, $keys);
					
			} else {
				$key = self::_prepare_item_attr_name($keys);
				foreach($items as $index => $item)
					$items[$index] = $item[$key];
			}
			
		}
		
		return $items;
	}

	
	public function get_item($index, $keys=null) {
		if(is_array($index)) $index = $index[0];
		$items = $this->get_items(array($index), $keys);
		return $items[0];
	}
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	
	public function get_xml($indented=false){ 
		
		$xml = $this->_xml;
		
		$rss_attrs = array('version'=>$this->_version);
		foreach($this->_namespaces as $name=>$value) $rss_attrs["xmlns:{$name}"] = $value;
		$xml->set_attr($rss_attrs);
		
		$channel_node = XML::create_long_node('channel');
		$xml->append_node($channel_node);
		
		$channel_node->add_node('title', XML::escape($this->_title));
		$channel_node->add_node('link', XML::escape(NavigationHelper::conv_abs_url($this->_link, $this->_base_url)));
		$channel_node->add_node('description', XML::escape(HTMLHelper::conv_relative_urls($this->_description, $this->_base_url)));
		$channel_node->add_node('lastBuildDate', XML::escape(self::_format_date($this->_last_build_date ? $this->_last_build_date : time())));
		$channel_node->add_node('pubDate', XML::escape(self::_format_date($this->_pub_date ? $this->_pub_date : ($this->_last_build_date ? $this->_last_build_date : time()))));
		if($this->_language) $channel_node->add_node('language', XML::escape($this->_language));
		if($this->_web_master) $channel_node->add_node('webMaster', XML::escape($this->_web_master));
		if($this->_editor) $channel_node->add_node('managingEditor', XML::escape($this->_editor));
		if($this->_category) $channel_node->add_node('category', XML::escape($this->_category));
		if($this->_generator) $channel_node->add_node('generator', XML::escape($this->_generator));
		if($this->_life_time) $channel_node->add_node('ttl', (integer) $this->_life_time);
		if($this->_image) {
			$image_node = XML::create_long_node('image');
			$image_node->add_node('url', XML::escape(NavigationHelper::conv_abs_url($this->_image, $this->_base_url)));
			$image_node->add_node('title', XML::escape($this->_title));
			$image_node->add_node('link', XML::escape(NavigationHelper::conv_abs_url($this->_link, $this->_base_url)));
			$channel_node->add_node($image_node);
		}		
		
		foreach($this->_items as $item)
			if(count($item) > 0) {
				
				$item_node = XML::create_long_node('item');
				$channel_node->add_node($item_node);
				
				if($item['title']) $item_node->add_node('title', XML::escape($item['title']));
				if($item['link']) $item_node->add_node('link', XML::escape(NavigationHelper::conv_abs_url($item['link'], $this->_base_url)));
				if($item['description']) $item_node->add_node('description', XML::escape(HTMLHelper::conv_relative_urls($item['description'], $this->_base_url)));
				if($item['autor']) $item_node->add_node('autor', XML::escape($item['autor']));
				if($item['category']) $item_node->add_node('category', XML::escape($item['category']));
				if($item['pubdate']) $item_node->add_node('pubDate', XML::escape(self::_format_date($item['pubdate'])));
				if($item['guid']) $item_node->add_node('guid', XML::escape($item['guid']));
			}
			
		$xml_str = $xml->get_xml($indented);
		
		$xml->clear_content();
		$xml->clear_attrs();
		
		return $xml_str;
	}
	
	
	//----------------------------------------------------------------------
	
	public function to_string() {
		return $this->get_xml(true);
	}
	
	public function out() {
		$mimetype = $this->get_mimetype();
		$charset = $this->get_charset();
		@ header("Content-Type: {$mimetype}; charset=\"{$charset}\"");
		echo $this->to_string();
	}
	
	public function save_to($filename) {
		@ file_put_contents($filename, $this->to_string());
	}

	
	public function out_attachment($filename=null) {
		NavigationHelper::header_content_attachment($filename ? $filename : 'rss.xml');
		$this->out();
	}
}



//---------------------------------------------------------------------- ?>