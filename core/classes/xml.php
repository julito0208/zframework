<?php 

class XML implements MIMEControl {

	
	protected static $_style_file_pattern = '/^(?i)\s*(?P<file>\S+)(?:\s+\(\s*(?P<attrs>.*?)\s*\))?\s*$/';
	protected static $_node_tree_split_pattern = '/\s*?(?:\:|\-\>|\s+)\s*/';

	protected static $_default_mimetype = 'text/xml';
	protected static $_default_version = '1.0';
	
	//----------------------------------------------------------------------
	
	/**
	 * 
	 * @return XML
	 */
	protected static function _parse_xml($str, $xml_node=null, $root_tag=false, &$header=null){
		
		$xml_node_varname = VariableHelper::global_var_unique($xml_node);
		$header_text_varname = VariableHelper::global_var_unique();
		$html_parse_listeners = array();
		
		$html_parse_listeners['open_tag'] = 
			create_function('$tagname, $attrs, $xml_node_varname, $root_tag, $header_text_varname', 
				' $parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if(!$parsing_xml_node) {
				 	if($root_tag) {
				 		$xml_node = XML::create($tagname);
				 		$xml_node->set_attr($attrs);
				 	} else $xml_node = XML::create_long_node($tagname, $attrs);
				 
				 	VariableHelper::global_var($xml_node_varname,$xml_node);
				 } else {
				 	$xml_node = XML::create_long_node($tagname, $attrs);
					$parsing_xml_node->append_node($xml_node);
					VariableHelper::global_var($xml_node_varname,$xml_node); }');
				
		
		$html_parse_listeners['short_tag'] =
			create_function('$tagname, $attrs, $xml_node_varname, $root_tag, $header_text_varname', 
				'$xml_node = XML::create_short_node($tagname, $attrs);
				 $parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if(!$parsing_xml_node) {
				 	if(!$root_tag){
				 		VariableHelper::global_var($xml_node_varname, $xml_node);
				 		return false;	
				 	}
				 
				 } else $parsing_xml_node->append_node($xml_node);');
				
		$html_parse_listeners['close_tag'] =
			create_function('$tagname, $xml_node_varname, $root_tag, $header_text_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if($parsing_xml_node) {
					$parent = $parsing_xml_node->get_parent();
					if(!$parent) return false; 
				 	else VariableHelper::global_var($xml_node_varname, $parent ); }');
		
		$html_parse_listeners['text'] =
			create_function('$text, $xml_node_varname, $root_tag, $header_text_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				if($parsing_xml_node) $parsing_xml_node->append_text($text);
				else VariableHelper::global_var($header_text_varname, $text);');
				

		$html_parse_listeners['comment'] =
			create_function('$comment, $xml_node_varname, $root_tag, $header_text_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				if($parsing_xml_node) $parsing_xml_node->append_comment($comment);');
			
		HTMLHelper::parse($str, $html_parse_listeners, $xml_node_varname, $root_tag, $header_text_varname);
	
		$header = VariableHelper::global_var_get($header_text_varname, true);
		return VariableHelper::global_var_get($xml_node_varname, true);
	}
	
	//----------------------------------------------------------------------
	
	protected static function _prepare_tagname($tagname) {
		
		$tagname = preg_replace('/[^\w]+/', '',$tagname);
		return preg_match('/^[A-Za-z][\w\-]*$/', $tagname) ? $tagname : false;
		
	}
	
	
	protected static function _prepare_comment($comment) {
		return '<!--'.str_replace('-->', '--&gt;', $comment).'-->';
	}
	
	
	protected static function _prepare_cdata($data) {
		return '<![CDATA['.str_replace(']]>', ']]&gt;', $data).']]>';
	}
	
	
	protected static function _escape_content($content) {
		
		$void_pattern = '/(?i)(?:(?:\<\!\-\-(?:(?:.|\s)*?)\-\-\>)|(?:\<\!\[cdata\[(?:(?:.|\s)*?)\]\]\>))/';
		$content = strval($content);
		$offset = 0;
		$escaped_content = '';
		
		while(preg_match($void_pattern, $content, $match, PREG_OFFSET_CAPTURE, $offset)) {
			$escaped_content.= self::escape(substr($content, $offset, $match[0][1]-$offset));
			$escaped_content.= $match[0][0];
			$offset = $match[0][1] + strlen($match[0][0]);
		}
		
		$escaped_content.= self::escape(substr($content, $offset));
		return $escaped_content;
	}
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	
	public static function escape($string) {
		
		if(!is_array($string)) 
			return str_replace(array("'", '"', '<', '>'), array('&apos;', '&quot;', '&lt;', '&gt;'), $string);
				
		else {
			
			$newstrings = array();
			
			foreach($string as $key=>$value)
				$newstrings[$key] = self::quote($value, $quote);
				
			return $newstrings;
			
		}
	}
	
	
	public static function quote($string){
		
		if(!is_array($string)) 
			return '"'.str_replace(array("'", '"', '<', '>'), array('&apos;', '&quot;', '&lt;', '&gt;'), $string).'"';
				
		else {
			
			$newstrings = array();
			
			foreach($string as $key=>$value)
				$newstrings[$key] = self::quote($value);
				
			return $newstrings;
			
		}
	}
		
	
	public static function unescape($string){
		
		if(!is_array($string)) 
			return str_replace( array('&apos;', '&quot;', '&lt;', '&gt;'), array("'", '"', '<', '>'), $string);
				
		else {
			
			$newstrings = array();
			
			foreach($string as $key=>$value)
				$newstrings[$key] = self::unescape($value, $quote);
				
			return $newstrings;
			
		}
	}
	
	
	
	//-------------------------------------------------------------------------
	
	public static function quote_attrs($attrs){
		
		$attrs_strs = '';
		
		foreach((array) $attrs as $key=>$value)
			if(!is_numeric(($key = trim(strtolower($key)))) && preg_match('/^[\w\-\:]+$/', $key)){
				
				if(is_bool($value)) { if($value) $attrs_strs.=" {$key}=".self::quote($key); }
				else if(!is_null($value)) $attrs_strs.= " {$key}=".self::quote( is_array($value) ? implode(' ', $value) : strval($value));
			}			
			
		return $attrs_strs;
	}
	
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	/**
	 *
	 * @return XML
	 *
	 */
	public static function create($tagname, $contents=null){
		$tagname = self::_prepare_tagname($tagname);
		if(!$tagname) return null;
		
		$args = func_get_args();
		
		$xml = new XML($tagname, array(), true, false);
		$xml->set_mimetype(self::$_default_mimetype);
		$xml->set_charset(ZPHP::get_config('charset'));
		$xml->set_version(self::$_default_version);
		call_user_func_array(array($xml,'append'),array_slice($args, 1));
		return $xml;		
	}
	
	
	
	/**
	 * 
	 * @return XML
	 *
	 */
	public static function parse($str){
		
		$xml = self::_parse_xml($str, null, true, $header);
		
		if($xml) {
			$num_matches = preg_match_all('/(?i)\<\?xml(?P<stylesheet>\-stylesheet)?(?:(?P<attrs>\s+(?:(?:\"(?:\\\\\\\\|\\\\\"|.)*?\")|(?:\\\'(?:\\\\\\\\|\\\\\\\'|.)*?\\\')|.|\s)*?))?\s*\?\>/', trim($header), $header_tags_match);
			for($i=0; $i<$num_matches; $i++) {
				$attrs = HTMLHelper::parse_attrs($header_tags_match['attrs'][$i], true);
				
				if($header_tags_match['stylesheet'][$i]) 
					$xml->add_style_files($attrs['href'], $attrs['media'], $attrs['type']);
			
				foreach($attrs as $key=>$value)
					switch($key){
						case 'version': $xml->set_version($value); break;
						case 'encoding': $xml->set_charset($value); break;
					}	
			}
			
		}
		
		return $xml;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 * 
	 */
	public static function load($filename){
		$str = @ file_get_contents($filename);
		$xml = self::parse($str);
		if($xml) $xml->set_file($filename);
		return $xml;
	}
	

	
	
	//----------------------------------------------------------------------
	
	/**
	 * 
	 * @return XML
	 */
	public static function parse_node($str){ return self::_parse_xml($str, null); }
	
	
	/**
	 * 
	 * @return XML
	 */
	public static function create_node($tagname, $contents=null ){
		$tagname = self::_prepare_tagname($tagname);
		if(!$tagname) return null;
		
		$args = func_get_args();
		
		$xml_node = new XML($tagname, $attrs, false, count($args) == 1);
		call_user_func_array(array($xml_node,'append'),array_slice($args, 1));
		return $xml_node;		
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public static function create_long_node($tagname, $attrs=array(), $contents=null ){
		$tagname = self::_prepare_tagname($tagname);
		if(!$tagname) return null;
		
		$args = func_get_args();
		
		$xml_node = new XML($tagname, $attrs, false, false);
		call_user_func_array(array($xml_node,'append'),array_slice($args, 2));
		return $xml_node;		
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public static function create_short_node($tagname, $attrs=array()){
		$tagname = self::_prepare_tagname($tagname);
		if(!$tagname) return null;
		
		$xml_node = new XML($tagname, $attrs, false, true);
		return $xml_node;		
	}

	
	
	public static function parse_nodes_array($str) {
		
		$xml_node_varname = VariableHelper::global_var_unique();
		$xml_nodes_array_varname = VariableHelper::global_var_unique(array());
		
		$html_parse_listeners = array();
		
		$html_parse_listeners['open_tag'] = 
			create_function('$tagname, $attrs, $xml_node_varname, $xml_nodes_array_varname', 
				'$xml_node = XML::create_long_node($tagname, $attrs);
				 $parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if(!$parsing_xml_node) {
				 	$nodes_array = VariableHelper::global_var($xml_nodes_array_varname);
				 	$nodes_array[] = $xml_node;
				 	VariableHelper::global_var($xml_nodes_array_varname, $nodes_array);
				 } else $parsing_xml_node->append_node($xml_node);
				VariableHelper::global_var($xml_node_varname,$xml_node); ');
				
		
		$html_parse_listeners['short_tag'] =
			create_function('$tagname, $attrs, $xml_node_varname, $xml_nodes_array_varname', 
				'$xml_node = XML::create_short_node($tagname, $attrs);
				 $parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if(!$parsing_xml_node) {
				 	$nodes_array = VariableHelper::global_var($xml_nodes_array_varname);
				 	$nodes_array[] = $xml_node;
				 	VariableHelper::global_var($xml_nodes_array_varname, $nodes_array);
				 } else $parsing_xml_node->append_node($xml_node);');
				
		$html_parse_listeners['close_tag'] =
			create_function('$tagname, $xml_node_varname, $xml_nodes_array_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				 if($parsing_xml_node) {
					$parent = $parsing_xml_node->get_parent();
					VariableHelper::global_var($xml_node_varname, $parent ); }');
		
		$html_parse_listeners['text'] =
			create_function('$text, $xml_node_varname, $xml_nodes_array_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				if($parsing_xml_node) $parsing_xml_node->append_text($text);');
				

		$html_parse_listeners['comment'] =
			create_function('$comment, $xml_node_varname, $xml_nodes_array_varname', 
				'$parsing_xml_node = VariableHelper::global_var($xml_node_varname);
				if($parsing_xml_node) $parsing_xml_node->append_comment($comment);');
			
		HTMLHelper::parse($str, $html_parse_listeners, $xml_node_varname, $xml_nodes_array_varname);
	
		VariableHelper::global_var_unset($xml_node_varname);
		return VariableHelper::global_var_get($xml_nodes_array_varname, true);
	}
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	protected $_attrs = array();
	protected $_contents = array();
	protected $_children = array();
	protected $_style_files;
	protected $_is_root = false;
	protected $_is_short;
	protected $_tagname;
	protected $_parent;
	protected $_mimetype;
	protected $_charset;
	protected $_version;
	protected $_file;
		
	
	protected function __construct($tagname, $attrs=array(), $root=false, $short=false){
		$this->_tagname = $tagname;
		$this->_attrs = CastHelper::to_array($attrs);
		$this->_is_short = (boolean) $short;
		$this->_is_root = (boolean) $root;
		
		if($this->is_root()) $this->_style_files = array();
	}
	
	
	public function __toString() { return $this->to_string(); }
	
	
	
	public function __toArray() { return $this->_attrs; }
	
	
	public function __set($name, $value=null) { return $this->set_attr($name, $value); }
	
	
	public function __get($name) { return $this->get_attr($name); }
	
	
	public function __isset($name) { return $this->has_attr($name); }
	
	
	public function __unset($name) { return $this->remove_attr($name); }
	
	
	
	//----------------------------------------------------------------------
			
	
	protected function _get_xml_lines($indent=false) {
		
		if($this->_tagname){
			
			$attrs_str = self::quote_attrs($this->_attrs);
			
			if($this->is_short()) return array("<{$this->_tagname}{$attrs_str} />");
			else {
			
				$open_tag_str = "<{$this->_tagname}{$attrs_str}>";
				$close_tag_str = "</{$this->_tagname}>";
				$contents_lines = array();
				
				foreach($this->_contents as $index=>$content) {
					if($indent && $index > 0) $contents_lines[] = ' ';
					
					if($content instanceof XML) $contents_lines = array_merge($contents_lines, $content->_get_xml_lines($indent));
					else $contents_lines = array_merge($contents_lines, explode("\n", self::_escape_content($content)));
				}
				
				if(count($contents_lines) > 1 || count($this->_children) > 0) {
					
					$indent_str = $indent ? '    ' : '';
					
					$xml_lines = array();
					$xml_lines[] = $open_tag_str;
					
					foreach($contents_lines as $line) $xml_lines[] = "{$indent_str}{$line}";
															
					$xml_lines[] = $close_tag_str;
					return $xml_lines;
					
				} else return array("{$open_tag_str}{$contents_lines[0]}{$close_tag_str}");
			}
								
		} else return array();
	}
	
	
	//----------------------------------------------------------------------
	
	/**
	 * 
	 * @return XML
	 */
	public function set_attr($arg1, $arg2=null){
		if(func_num_args() == 2) $attrs = array($arg1=>$arg2);
		else if(!is_array($arg1)) $attrs = HTMLHelper::parse_attrs($arg1);
		else $attrs = $arg1;
		
		foreach($attrs as $key=>$value) {
			$key = trim($key);
			if(is_null($value)) unset($this->_attrs[$key]);
			else $this->_attrs[$key] = $value;
		}
								
		return $this;
	}
	
	
	public function get_attr($name=null){
		if(func_num_args() > 0) return $this->_attrs[trim($name)];
		else return $this->_attrs;
	}
	
	
	public function has_attr($name) {
		return array_key_exists(trim($name), $this->_attrs);
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function attr($arg1, $arg2=null){
		$args = func_get_args();
		$num_args = count($args); 
		if($num_args == 1 && !is_array($arg1) && preg_match('/^[\w\-\.\:]+$/', $arg1)) return call_user_func_array(array($this,'get_attr'), $args);
		else return call_user_func_array(array($this,'set_attr'), $args);
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function remove_attr($name=null) {
		foreach(func_get_args() as $name)
			unset($this->_attrs[trim($name)]);
			
		return $this;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function clear_attrs() {
		$this->_attrs = array();
		return $this;
	}
	
	//----------------------------------------------------------------------
	
		
	/**
	 * 
	 * @return XML
	 */
	public function append_text($text=null){
		if($this->is_short()) return $this;
		if(is_string($this->_contents[($index = (count($this->_contents)-1))])) $this->_contents[$index].= $text;
		else if($text) $this->_contents[] = strval($text);
		return $this;
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add_text($text=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append_text'), $args);
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_text($text=null){
		if($this->is_short()) return $this;
		if(is_string($this->_contents[0])) $this->_contents[0].= $text;
		else if($text) array_unshift($this->_contents[], strval($text));
		return $this;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append_comment($comment=null){
		if(trim($comment)) $this->append_text(self::_prepare_comment($comment));
		return $this;
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add_comment($comment=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append_comment'), $args);
	}
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_comment($comment=null){
		if(trim($comment)) $this->prepend_text(self::_prepare_comment($comment));
		return $this;
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append_cdata($data=null){
		if(trim($data)) $this->append_text(self::_prepare_cdata($data));
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_cdata($data=null){
		if(trim($data)) $this->prepend_text(self::_prepare_cdata($data));
		return $this;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_text($text=null){
		if($this->is_short()) return $this;
		$this->clear_content();
		$this->append_text($text);
		return $this;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function clear_text() {
		if($this->is_short()) return $this;
		$old_contents = $this->_contents;
		$this->_contents = array();

		foreach($old_contents as $content)
			if($content instanceof XML)
				$this->_contents[] = $content;
			
		return $this;
	}
	
	
	
	public function get_text() {
		$text = '';
		foreach($this->_contents as $content)
			if(!($content instanceof XML))
				$text.= $content;
				
		return $text;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append_node($node, $content=null) {
		if($this->is_short() || is_null($node)) return $this;
		
		if($node instanceof XML) {
			$this->_contents[] = $node;
			$this->_children[] = $node;
			$node->_parent = $this;
			return $this;
		} else {
			$args = func_get_args();
			return $this->append_node(call_user_func_array(array(self, 'create_node'), $args));
		}
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add_node($node, $content=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append_node'), $args);
	}
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_node($node, $content=null) {
		if($this->is_short() || is_null($node)) return $this;
		
		if($node instanceof XML) {
			array_unshift($this->_contents[], $node);
			array_unshift($this->_children[], $node);
			$node->_parent = $this;
			return $this;
		} else {
			$args = func_get_args();
			return $this->prepend_node(call_user_func_array(array(self, 'create_node'), $args));
		}
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append_short_node($tagname, $attrs=array()) {
		if($this->is_short()) return $this;
		$args = func_get_args();
		return $this->append_node(call_user_func_array(array(self, 'create_short_node'), $args));
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add_short_node($tagname, $attrs=array()){
		$args = func_get_args();
		return call_user_func_array(array($this,'append_short_node'), $args);
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append_long_node($tagname, $attrs=array(), $contents=null) {
		if($this->is_short()) return $this;
		$args = func_get_args();
		return $this->append_node(call_user_func_array(array(self, 'create_long_node'), $args));
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add_long_node($tagname, $attrs=array(), $contents=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append_long_node'), $args);
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_short_node($tagname, $attrs=array()) {
		if($this->is_short()) return $this;
		$args = func_get_args();
		return $this->prepend_node(call_user_func_array(array(self, 'create_short_node'), $args));
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_long_node($tagname, $attrs=array(), $contents=null) {
		if($this->is_short()) return $this;
		$args = func_get_args();
		return $this->prepend_node(call_user_func_array(array(self, 'create_long_node'), $args));
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_children($node=null){
		if($this->is_short()) return $this;
		$this->clear_content();
		$args = func_get_args();
		foreach(func_get_args() as $contents)
			foreach((is_array($contents) ? $contents : array($contents)) as $content)
				if($content instanceof XML) $this->append_node($content);
			
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function remove_node($node=null) {
		if($this->is_short()) return $this;
		$remove_nodes = array();
		foreach(func_get_args() as $nodes)
			foreach((is_array($nodes) ? $nodes : array($nodes)) as $node)
				if($node instanceof XML) $remove_nodes[] = $node;
		
		$old_contents = $this->_contents;
		$this->_contents = array();
		$this->_children = array();
		
		foreach($old_contents as $content)
			if(!in_array($content, $remove_nodes)){
				if($content instanceof XML) {
					$this->_contents[] = $content;
					$this->_children[] = $content;
				} else $this->append_text($content);
							
			} else $content->_parent = null;
		
		
				
		return $this;
	}
	
	
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function clear_children() {
		if($this->is_short()) return $this;
		
		$text = '';
		foreach($this->_contents as $content)
			if($content instanceof XML) $content->_parent = null;
			else $text.= $content;
			
		$this->_contents = array();
		$this->_children = array();
				
		return $this->append_text($text);
	}
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function get_node($key) {
				
		if(is_numeric($key)) return $this->_children[$key];
		else if($key) {
			
			$tagname = trim($key);
			foreach($this->_children as $node)
				if($node->get_tagname() == $tagname)
					return $node;
					
		}
		
		return null;
		
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function find_node($key){
		
		list($key, $rest_key) = (array) preg_split(self::$_node_tree_split_pattern, $key, 2);
		$node = $this->get_node($key);
		
		if($node) return $rest_key ? $node->find_node($rest_key) : $node;
		else return null;
	}
	
	
		
	public function get_nodes($keys=null) {
		if($this->is_short()) return array();
		
		if(func_num_args()==0) return $this->get_children();
		else {
			
			$nodes = array();
			$used_keys = array();
			
			foreach(func_get_args() as $keys)
				foreach((is_array($keys) ? $keys : array($keys)) as $key)
					if(in_array($key, $used_keys)) continue;
					else {
						$used_keys[] = $key;
						
						if(is_numeric($key)) {
							if(($node = $this->_children[$key])) $nodes[] = $node;
						
						} else if($key) {
							
							$tagname = trim($key);
							foreach($this->_children as $node)
								if($node->get_tagname() == $tagname)
									$nodes[] = $node;
									
						}
					}
					
			
			return $nodes;			
		}
	}
	
	
	
	public function get_children() { return $this->_children; }
	
	
	
	public function count_children() { return count($this->_children); }
	
	
	
	public function count_nodes() { return $this->count_children(); }
	
	
	public function has_node($node) { 
		if($node instanceof XML) return in_array($node, $this->_children); 
		else return !is_null($this->get_node($node));
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function append($content=null){
		if($this->is_short()) return $this;
		$parse_text = '';			
		
		foreach(func_get_args() as $contents)
			foreach((is_array($contents) ? $contents : array($contents)) as $content)
				if($content instanceof XML) {
					if(trim($parse_text)) self::_parse_xml($parse_text, $this);
					$this->append_node($content);
					$parse_text = '';
				} else $parse_text.= strval($content);
				
				
		if(trim($parse_text)) self::_parse_xml($parse_text, $this);		
		
		return $this;
	}
	
	/**
	 * 
	 * @return XML
	 */
	public function add_content($content=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append'), $args);
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function add($content=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'append'), $args);
	}
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend($content=null){
		if($this->is_short()) return $this;
		
		$old_contents = $this->_contents;
		$this->_contents = array();
		$this->_children = array();
				
		$args = func_get_args();
		call_user_func_array(array($this,'append'), $args);
		
		if(is_string($old_contents[0])) {
			$text = array_shift($old_contents);
			$this->append_text($text);			
		}
		
		foreach($old_contents as $content) {
			$this->_contents[] = $content;
			if($content instanceof XML)
				$this->_children[] = $content;			
		}
		
		
		return $this;
	}
	
	/**
	 * 
	 * @return XML
	 */
	public function clear_content(){
		if($this->is_short()) return $this;
		foreach($this->_children as $node) $node->_parent = null;
		$this->_contents = array();
		$this->_children = array();
		return $this;
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_content($content=null){
		if($this->is_short()) return $this;
		$this->clear_content();
		$args = func_get_args();
		return call_user_func_array(array($this,'append'), $args);
	}
	
	
	
	public function get_contents() { return $this->_contents; }
	
	
	//----------------------------------------------------------------------
	//----------------------------------------------------------------------
	
	
	public function is_short() { return $this->_is_short; }
	
	
	public function is_long() { return !$this->is_short(); }
	
	
	public function is_root() { return $this->_is_root; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function get_parent() { return $this->_parent; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function get_root() { 
		if($this->_parent) return $this->_parent->get_root();
		else return $this;
	}
	
		
	
	public function has_parent() { return (boolean) $this->_parent; }
	
	
	public function get_tagname() { return $this->_tagname; }
	
	
	//----------------------------------------------------------------------
	
	/**
	 * 
	 * @return XML
	 */
	public function append_to(XML $node){
		$node->append_node($this);
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function prepend_to(XML $node){
		$node->prepend_node($this);
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function remove(){
		if($this->_parent) $this->_parent->remove_node($this);
		return $this;
	}
	
	
	
	//----------------------------------------------------------------------
	
	/**
	 * 
	 * @return XML
	 */
	public function set_mimetype($mimetype){
		$this->_mimetype = MimeTypeHelper::mimetype($mimetype, ZPHP_XML_MIMETYPE);
		return $this;
	}
	
	
	public function get_mimetype() { return $this->_mimetype; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function mimetype($mimetype=null){
		if(func_num_args() > 0) return $this->set_mimetype($mimetype);
		else return $this->get_mimetype();
	}
	
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_charset($charset){
		$this->_charset = trim(str_replace(array("\n", '"'), '', $charset));
		return $this;
	}
	
	
	public function get_charset() { return $this->_charset; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function charset($charset=null){
		if(func_num_args() > 0) return $this->set_charset($charset);
		else return $this->get_charset();
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_encoding($encoding) { return $this->set_charset($encoding); }
	
	
	public function get_encoding() { return $this->get_charset(); }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function encoding($encoding=null){
		$args = func_get_args();
		return call_user_func_array(array($this,'charset'), $args);
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_version($version){
		$this->_version = trim(str_replace(array("\n", '"'), '', $version));
		return $this;
	}
	
	
	public function get_version() { return $this->_version; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function version($version=null){
		if(func_num_args() > 0) return $this->set_version($version);
		else return $this->get_version();
	}
	
	
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_file($file){
		$this->_file = $file;
		return $this;
	}
	
	
	public function get_file() { return $this->_file; }
	
	
	/**
	 * 
	 * @return XML
	 */
	public function file($file=null){
		if(func_num_args() > 0) return $this->set_file($file);
		else return $this->get_file();
	}
	
	
	//------------------------------------------------------------------------------------
		
	/**
	 * 
	 * @return XML
	 */
	public function add_style_files($files, $media=null, $type=null){
		
		if($type) $type = MimeTypeHelper::mimetype($type);
		$media = trim($media);
		$default_media = $media ? $media : 'screen';
		
		foreach((array) $files as $files_str) 
			foreach(preg_split('/\s*(?:\;|\|)\s*/', trim($files_str)) as $file_str) 
				if(preg_match(self::$_style_file_pattern, $file_str, $file_match)) {
					$file_media = $default_media;
					$file_type = $type;
					$file = $file_match['file'];
					
					if($file_match['attrs']) {
						$num_matches = preg_match_all('/\s+(?P<name>[\w\.\:\-]+)\s*(?:\:|\=)\s*(?P<value>\S+)/', ' '.$file_match['attrs'], $attrs_matches);
						for($i=0; $i<$num_matches; $i++) {
							$attr_name = strtolower($attrs_matches['name'][$i]);
							if($attr_name == 'media') $file_media = $attrs_matches['value'][$i];
							else if($attr_name == 'type') $file_type = MimeTypeHelper::mimetype($attrs_matches['value'][$i]);
						}
					}
					
					$this->_style_files[$file] = array('file' => NavigationHelper::conv_abs_url($file), 'media' => $file_media ? $file_media : $default_media, 'type' => $file_type ? $file_type : FilesHelper::file_get_mimetype($file));
				}
			
		
		return $this;
	}
	
	
	
	public function has_style_file($file) { 
		if(preg_match(self::$_style_file_pattern, $file, $file_match)) return array_key_exists($file_match['file'], $this->_style_files); 
		else return false;
	}
	
	
	
	public function get_style_file_media($file) { 
		if(preg_match(self::$_style_file_pattern, $file, $file_match)) return $this->_style_files[$file_match['file']]['media']; 
		else return null;
	}
	
	
	
	public function get_style_file_type($file) { 
		if(preg_match(self::$_style_file_pattern, $file, $file_match)) return $this->_style_files[$file_match['file']]['type']; 
		else return null;
	}
	
	
	public function get_style_files($get_info=false) { 
		return $get_info ? array_values($this->_style_files) : array_keys($this->_style_files); 
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function remove_style_files($file){
		foreach(func_get_args() as $arg)
			foreach((array) $arg as $files_str) 
				foreach(preg_split('/\s*(?:\:|\;|\|)\s*/', trim($files_str)) as $file_str) 
					if(preg_match(self::$_style_file_pattern, $file_str, $file_match)) 
						unset($this->_style_files[$file_match['file']]);		
				
		return $this;
		
	}
	
	
	/**
	 * 
	 * @return XML
	 */
	public function clear_style_files(){
		$this->_styles_files = array();
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function set_style_files($file, $media=null, $type=null){
		$this->clear_style_files();
		$args = func_get_args();
		return call_user_func_array(array($this, 'add_style_files'), $args);
	}
	
	
	
	/**
	 * 
	 * @return XML
	 */
	public function style_files($arg=null){
		if(func_num_args() > 0) {
			$args = func_get_args();
			return call_user_func_array(array($this, 'add_style_files'), $args);
		} else return $this->get_style_files();
	}
	
	
	public function count_style_files() { return count($this->_style_files); }
	
	
	
	//----------------------------------------------------------------------
	
	
	public function get_xml($indented=false){ 
		
		if($this->is_root()) {
			$xml = '<?xml'.self::quote_attrs(array_filter(array('version'=> $this->_version ? $this->_version : '1.0', 'encoding'=>$this->_charset)))."?>\n\n";
			if(count($this->_style_files) > 0) {
				
				foreach($this->_style_files as $style_file)
					$xml.= "<?xml-stylesheet".self::quote_attrs(array('href'=>$style_file['file'], 'type'=>$style_file['type'], 'media'=>$style_file['media']))."?>\n";
					
				$xml.= "\n";
			}
		}
		
		$xml.= implode("\n", $this->_get_xml_lines($indented));
		return $xml;
	}
	
	
	//----------------------------------------------------------------------
	
	public function to_string() {
		return $this->get_xml();
	}
	
	public function out() {
		@ header("Content-Type: {$this->_mimetype}; charset=\"{$this->_charset}\"");
		echo $this->to_string();
	}
	
	public function save_to($filename) {
		@ file_put_contents($filename, $this->to_string());
	}

	public function out_attachment($filename=null) {
		NavigationHelper::header_content_attachment($filename ? $filename : 'xml.xml');
		$this->out();
	}
}



//---------------------------------------------------------------------- ?>