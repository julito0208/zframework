<?php 

class HTMLHelper {

	const DATE_SEPARATOR = "<span style='padding: 0 1px'>/</span>";
	const DATETIME_SEPARATOR = "<span style='padding: 0 3px'>-</span>";
	const DATETIME_LITERAL_SEPARATOR = " - ";
	const DATE_FORMAT_LITERAL = '%s';
	const DATE_FORMAT_SHORT = "%d<span style='padding: 0 3px'>-</span>%m<span style='padding: 0 3px'>-</span>%y";
	const DATE_FORMAT_LONG = "%d<span style='padding: 0 3px'>-</span>%m<span style='padding: 0 3px'>-</span>%Y";
	const TIME_FORMAT = "%H<span style='padding: 0 0px'>:</span>%M hs";

	const DATE_FORMAT_SHORT_LITERAL = true;
	const DATE_FORMAT_LONG_LITERAL = true;
	const DATETIME_FORMAT_SHORT_LITERAL = true;
	const DATETIME_FORMAT_LONG_LITERAL = true;
	const DATE_FORMAT_SHOW_LITERAL = true;

	public static function escape($string) {
		return self::quote($string, '');
	}
	
	public static function escape_quotes($string)
	{
		return str_replace(array("'", '"'), array('&#039;', '&quot;'), $string);
	}
	
	public static function escape_tags($string)
	{
		return str_replace(array("<", ">"), array('&lt;', '&gt;'), $string);
	}
	
	public static function quote($string, $quote='"'){
		
		if(!is_array($string)) 
			return $quote . str_replace(array("'", '\\', '�', '"'), array('&#039;', '&#092;', '&euro;', '&quot;'), htmlspecialchars($string, null, ZPHP::get_config('html_charset'))) . $quote;
				
		else {
			
			$newstrings = array();
			
			foreach($string as $key=>$value)
				$newstrings[$key] = self::quote($value, $quote);
				
			return $newstrings;
			
		}
	}
		
	
	public static function unescape($string) {
		
		if(!is_array($string)) 
			return str_replace(array('&#039;', '&#092;', '&apos;'), array("'", '\\', "'"), html_entity_decode($string));
		
		else {
			
			$newstrings = array();
			
			foreach($string as $key=>$value)
				$newstrings[$key] = self::unescape($value, $quote);
				
			return $newstrings;
			
		}
	}

	public static function prepare_url_text($text)
	{
		return preg_replace('#[^A-Za-z0-9\-]+#', '', str_replace(' ', '-', $text));
	}
	
	
	//-------------------------------------------------------------------------
	
	
	public static function cdata($data) {
		return '<![CDATA['.str_replace(']]>', ']]&gt;', $data).']]>';
	}
	
	
	//-------------------------------------------------------------------------
	
	
	public static function encode_array($array) {
		return array_keys_encode_html($array);
	}
	
	
	
	public static function format_date($date, $format_date=self::DATE_FORMAT_LONG, $format_time=null, $literal=self::DATE_FORMAT_SHORT_LITERAL) {
		
		if($literal) {
				
			$diff_days = DateHelper::time_diff_days(time(), $date);
			
			if(abs($diff_days) <= 1) {
			
				switch ($diff_days)	{
					
					case 1: $day_literal = 'Ayer'; break;
					
					case 0: $day_literal = 'Hoy'; break;
					
					case -1: $day_literal = 'Ma�ana'; break;
				}
				
				
				$date_str = sprintf(self::DATE_FORMAT_LITERAL, $day_literal);
				
				
			} else $literal = false;	
		}
		
		
		if(!$literal) $date_str = strftime($format_date, $date);
		
		
		if($format_time) {
			
			$time_str = strftime($format_time, $date);
			
			if($literal) return $date_str.self::DATETIME_LITERAL_SEPARATOR.$time_str;
			else return $date_str.self::DATETIME_SEPARATOR.$time_str;
			
		} else return $date_str;
		
	}
	
	
	public static function format_date_short($date, $literal=self::DATE_FORMAT_SHORT_LITERAL) {
		return self::format_date($date, self::DATE_FORMAT_SHORT, null, $literal);
	}
	
	
	public static function format_date_long($date, $literal=self::DATE_FORMAT_LONG_LITERAL) {
		return self::format_date($date, self::DATE_FORMAT_LONG, null, $literal);
	}
	
	
	
	public static function format_datetime_short($date, $literal=self::DATETIME_FORMAT_SHORT_LITERAL) {
		return self::format_date($date, self::DATE_FORMAT_SHORT, self::TIME_FORMAT, $literal);
	}
	
	
	public static function format_datetime_long($date, $literal=self::DATETIME_FORMAT_LONG_LITERAL) {
		return self::format_date($date, self::DATE_FORMAT_LONG, self::TIME_FORMAT, $literal);
	}
	
	
	
	//-------------------------------------------------------------------------
	
	public static function quote_attrs($attrs, $quote='"'){
		
		$attrs_strs = '';
		
		foreach((array) $attrs as $key=>$value)
			if(!is_numeric(($key = trim($key))) && preg_match('/^[\w\-\:]+$/', $key)){
				
				if(is_bool($value)) { 
					
					if($value) $attrs_strs.=" {$key}={$quote}{$key}{$quote}"; 
				
				} else if(!is_null($value)) {
					
					if(is_array($value)) {
						
						if(count($value) > 0) $attrs_strs.= " {$key}=".self::quote(implode(' ', $value), $quote);
						
					} else $attrs_strs.= " {$key}=".self::quote(strval($value), $quote);
				}
			}
					
		return $attrs_strs;
	}
	
	
	public static function short_tag($tagname, $attrs=array()) {
		
		$tagname = trim(strtolower($tagname));
		$attrs = array();
		
		$args = func_get_args();
		foreach(array_slice($args, 1) as $arg) 
			$attrs = array_merge($attrs, (array) $arg);
			
		return "<{$tagname}". self::quote_attrs($attrs) . " />";
		
	}
	
	
	
	
	public static function wrap_tag($tagname, $content='', $attrs=array()) {
		
		$tagname = trim(strtolower($tagname));
		$attrs = array();
		
		$args = func_get_args();
		foreach(array_slice($args, 2) as $arg) 
			$attrs = array_merge($attrs, (array) $arg);
			
		return "<{$tagname}". self::quote_attrs($attrs) . ">{$content}</{$tagname}>";
		
	}
	
	
	public static function long_tag($tagname, $content='', $attrs=array()) {
		
		$tagname = trim(strtolower($tagname));
		$attrs = array();
		$content = self::escape($content);
		
		$args = func_get_args();
		foreach(array_slice($args, 2) as $arg) 
			$attrs = array_merge($attrs, (array) $arg);
			
		return "<{$tagname}". self::quote_attrs($attrs) . ">{$content}</{$tagname}>";
		
	}
	
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	
	
	public static function conv_relative_urls($html, $base_url=null){
		
		$base_url = rtrim($base_url ? $base_url : ZPHP::get_config('html_base_url'), '/ ');
		
		if($base_url) {
			$pattern = '/(?i)(?s)(?P<attr_open>(?:url|href|src)\s*(?:(?P<parenthesis>\()|(?:\=\s*(?P<quote>\"|\\\'))))\s*\/(?P<url>\S*)\s*(?P<attr_close>(?(parenthesis)\)|(\k<quote>)))/';
			$html = preg_replace($pattern, "\\1{$base_url}/\\4\\5", $html);
		}
		
		return $html;
	}
	
	
	public static function parse_attrs($str, $to_lower=false){
		$attrs = array();
		if($str){
			$num_attrs = preg_match_all('/\s+(?P<name>[\w\-\.\:]+)\s*\=\s*(?:(?:(?P<quote>\"|\\\')(?P<quoted_value>(?:\\\\\\\\|\\\\(\k<quote>)|.|\s)*?)(\k<quote>))|(?P<direct_value>\S+))/', ' '.$str, $attrs_matches);
			for($i=0; $i<$num_attrs; $i++){
				if($attrs_matches['direct_value'][$i]) $attr_value = $attrs_matches['direct_value'][$i]; 
				else $attr_value = str_replace(array('\\'.$attrs_matches['quote'][$i], '\\\\'), array($attrs_matches['quote'][$i], '\\'), $attrs_matches['quoted_value'][$i]);
						
				$attr_name = $attrs_matches['name'][$i];			
				$attrs[$to_lower ? strtolower($attr_name) : $attr_name] = self::unescape($attr_value);
			}
		}
		
		return $attrs;
		
	}
	
	
	public static function parse($str, $listeners=array(), $parameters=null) {
		
		$args = func_get_args();
		$additional_parameters = array_slice($args, 2);
		
		$str = strval($str);
		$listeners = (array) $listeners;
			
		$tag_pattern = '\<(?P<tagname>[A-Za-z][\w\-]*)(?:(?P<tag_attrs>\s+(?:(?:\"(?:\\\\\\\\|\\\\\"|.)*?\")|(?:\\\'(?:\\\\\\\\|\\\\\\\'|.)*?\\\')|.|\s)*?))?\s*(?P<short_tag>\/)?>';
		$close_tag_pattern = '\<\/(?P<close_tagname>[A-Za-z][\w\-]*)\s*\>';
		$comment_pattern = '\<\!\-\-(?P<comment_data>(?:.|\s)*?)\-\-\>';
		
		$parse_pattern = "/(?P<tag>{$tag_pattern})|(?P<close_tag>{$close_tag_pattern})|(?P<comment>{$comment_pattern})/";
		$offset = 0;
		
		$break = false;
		$opened_tagnames = array();
		
		while(!$break && preg_match($parse_pattern, $str, $match, PREG_OFFSET_CAPTURE, $offset )) {
			
			if($listeners['text']) {
			
				$text = substr($str, $offset, $match[0][1]-$offset);
				if(trim($text)) {
					if(call_user_func_array($listeners['text'], array_merge(array(self::unescape($text)), $additional_parameters)) === false){
						$break = true;
						break;
					}
				}
			}
			
			if($match['comment'] && $listeners['comment']) $break = call_user_func_array($listeners['comment'],array_merge(array(self::unescape($match['comment_data'][0])), $additional_parameters)) === false;
			
			else if($match['close_tag'] && $opened_tagnames[0] == strtolower($match['close_tagname'][0])){ 
				
				array_shift($opened_tagnames);
				if($listeners['close_tag']) $break = call_user_func_array($listeners['close_tag'],array_merge(array($match['close_tagname'][0]), $additional_parameters)) === false;
			}
			
			else if($match['tag']) {
				
				
				$tagname = $match['tagname'][0];
						
				$is_short_tag = ((boolean) $match['short_tag']) || in_array(($lower_tagname = strtolower($tagname)), array('br','hr'));
				$listener_name = $is_short_tag ? 'short_tag' : 'open_tag';
				
				if(!$is_short_tag) array_unshift($opened_tagnames, $lower_tagname);
				
				if($listeners[$listener_name]) {
				
					$attrs = self::parse_attrs($match['tag_attrs'][0]);
					$break = call_user_func_array($listeners[$listener_name], array_merge(array($tagname, $attrs), $additional_parameters)) === false;
				}
			}
			
			$offset = $match[0][1] + strlen($match[0][0]);
		}
		
		
		if(!$break && $listeners['text']){
			$text = substr($str, $offset);
			if(trim($text)) call_user_func_array($listeners['text'],array_merge(array(self::unescape($text)), $additional_parameters));
		}
		
	}
	
	
	
	//-------------------------------------------------------------------------
	
	
	
	public static function query_make($data, $arg_separator=null) {
		return http_build_query($data, $arg_separator);
	}
	
	
	public static function query_out($data, $arg_separator=null, $charset=null) {
		
		NavigationHelper::header_content_text_plain($charset ? $charset : self::CHARSET);
		
		echo self::query_make($data, $arg_separator);
		
	}
	
	
	
	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	
	
	public static function style_parse($arg1=null, $arg2=null) {
		
		
		$style = "";
		
		$args = func_get_args();
		
		
		foreach($args as $arg) {
	
			if(is_array($arg)) {
				
				if(ArrayHelper::is_numeric($arg)) {
					
					$style.= call_user_func_array('html_style_parse', $arg);
					
				} else {
					
					foreach($arg as $key => $value) {
						
						$style.= strtolower(trim($key)).': '. trim($value).'; ';
						
					}
					
				}
				
				
			} else {
				
				foreach(explode(";", $arg) as $style_def) {
					
					$style_def_parts = explode(":", $style_def);
					
					if(count($style_def_parts) > 1 && trim($style_def_parts[0])) {
						
						$style.= strtolower(trim($style_def_parts[0])).': '. trim($style_def_parts[1]).'; ';
						
					}
					
					
				}
			}
			
			
		}
		
		
		
		return $style;
		
		
	}
	
	
	
	public static function fix_incomplete($html) {
	
		$html = str_replace('</>', '',$html);
		$html = str_replace('<!--c-->', '',$html);
	
		$tab = 0;  //deepness of the tree
		$new = ''; //new html string
		$pos = 0; //wich character i'n analyzing
		$tagsStack = array(); //stack of read tags
	
		$ignored = array('br', 'img'); //this tags has no close one so i ignore them
	
		while ($pos < strlen($html))
		{
			if ($html[$pos] == '<') //a tag starts
			{
				if ($html[$pos+1] == '/') //close tag
				{
					
					$tag = '';
					$search_tag_pos = $pos+2;
	
					while ($html[$search_tag_pos] != ' ' && $html[$search_tag_pos] != '>')
					{
						$tag .= $html[$search_tag_pos];
						$search_tag_pos++;
					}
					
	
					if (!in_array($tag, $ignored) && $tab > 0) // force to be the same as the opened one
					{
							$lastTag = $tagsStack[$tab];
							$new .= '</'.$lastTag;
							$pos = strpos ($html, '>', $pos);
	
							unset ($tagsStack[$tab]);
	
							$tab--;
					}
					elseif ($tab == 0) //if there is no more opened tags, i ignore this close one
					{
						$pos = strpos ($html, '>', $pos) +1;
						continue;
					}
				}
				else
				{
					
					$tag = '';
					$search_tag_pos = $pos+1;
	
					while ($html[$search_tag_pos] != ' ' && $html[$search_tag_pos] != '>')
					{
						$tag .= $html[$search_tag_pos];
						$search_tag_pos++;
					}
	
	
					if (!in_array($tag, $ignored))
					{
						$tab++;
	
						$tagsStack[$tab] = $tag;
					}
				}
			}
	
			$new .= $html[$pos];
			$pos++;
	
		}
	
		foreach ($tagsStack as $tag) //there are unclosed tags, so i force to close them
		{
			$new .= '</'.$tag.'>';
		}
	
		return $new;
	}

	
}