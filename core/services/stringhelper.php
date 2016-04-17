<?php //-------------------------------------------------------------------- 

class StringHelper {

	
	public static function str($var) {
		return (string) $var;
	}
	
	//-------------------------------------------------------------------- 
	
	public static function escape($string){
		
		if(!is_array($string)) 
			return str_replace(array('"', "'", '\\'), array('\"', "\'", '\\\\'), (string) $string);
			
		else {
			
			$newstrings = array();
			foreach($string as $key=>$value)
				$newstrings[$key] = self::escape($value);
				
			return $newstrings;
		}
		
	}
	
	public static function unescape($string){
		
		if(!is_array($string)) 
			return str_replace(array('\"', "\'", '\\\\'), array('"', "'", '\\'), (string) $string);
			
		else {
			
			$newstrings = array();
			foreach($string as $key=>$value)
				$newstrings[$key] = self::unescape($value);
				
			return $newstrings;
		}
		
	}
	
	//-------------------------------------------------------------------- 
	
	
	public static function starts_with($str, $prefix, $case_insensitive=false) {

		if(strlen($prefix) > 0) {
			$func_name = $case_insensitive ? 'stripos' : 'strpos';
			return call_user_func($func_name, $str, $prefix) === 0;
		} else {
			return true;
		}
	}
	
	
	
	
	public static function ends_with($str, $sufix, $case_insensitive=false) {
		
		if(strlen($sufix) > 0) {
			$func_name = $case_insensitive ? 'strripos' : 'strrpos';
			return call_user_func($func_name, $str, $sufix) === strlen($str) - strlen($sufix);
		} else {
			return true;
		}
	}
	
	
	public static function put_prefix($str, $prefix, $case_insensitive=false) {
		if(!self::starts_with($str, $prefix, $case_insensitive)) $str = $prefix . $str;
		return $str;
	}
	
	
	public static function put_sufix($str, $sufix, $case_insensitive=false) {
		if(!self::ends_with($str, $sufix, $case_insensitive)) $str.= $sufix;
		return $str;
	}
	
	
	
	
	public static function remove_prefix($str, $prefix, $case_insensitive=false) {

		if(is_array($prefix))
		{
			foreach($prefix as $p)
			{
				$nstr = self::remove_prefix($str, $p, $case_insensitive);

				if($nstr != $str)
				{
					return $nstr;
				}
			}

			return $str;
		}
		else
		{
			if(self::starts_with($str, $prefix, $case_insensitive)) $str = substr($str, strlen($prefix));
			return $str;
		}
	}
	
	
	public static function remove_sufix($str, $sufix, $case_insensitive=false) {

		if(is_array($sufix))
		{
			foreach($sufix as $s)
			{
				$nstr = self::remove_sufix($str, $s, $case_insensitive);

				if($nstr != $str)
				{
					return $nstr;
				}
			}

			return $str;
		}
		else
		{
			if(self::ends_with($str, $sufix, $case_insensitive)) $str = substr($str, 0, strlen($str) - strlen($sufix));
			return $str;
		}

	}
	
	
	//--------------------------------------------------------------------
	
	
	public static function in_str($needle, $haystack, $offset=null) {
		return strpos($haystack, $needle, $offset) !== false;
	}
	
	
	public static function in_stri($needle, $haystack, $offset=null) {
		return stripos($haystack, $needle, $offset) !== false;
	}
	
	
	//--------------------------------------------------------------------
	
	public static function trim($strings, $remove_extra_spaces=false) {
		
		if(is_array($strings)) {
			
			$new_strings = array();
			foreach($strings as $index => $value)
				$new_strings[$index] = self::trim($value, $remove_extra_spaces);
				
			return $new_strings;
			
		} else if($remove_extra_spaces) return preg_replace('#\s+#', ' ', trim((string) $strings));
		
		else return trim((string) $strings);
		
	}
	
	
	
	
	//--------------------------------------------------------------------
	
	
	public static function lower($str) {
		
		$str = strtolower($str);
	
		$str = str_replace(array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), array('á', 'é', 'í', 'ó', 'ú', 'ñ'), $str);
		
		return $str;
		
	}
	
	
	
	
	public static function upper($str) {
		
		$str = strtoupper($str);
	
		$str = str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'), $str);
		
		return $str;
		
	}
	
	
	
	public static function capitalize($str, $each_word=true) {
		
		if($each_word) {
			
			$paragraphs = explode("\n", $str);
			$new_paragraphs = array();
			
			foreach($paragraphs as $paragraph) {
				
				$tabs = explode("\t", $paragraph);
				$new_tabs = array();
				
				foreach($tabs as $tab) {
					
					$words = explode(" ", $tab);
					$new_words = array();
					
					foreach($words as $word) 
					
						$new_words[] = self::capitalize($word, false);
						
					
					$new_tabs[] = implode(" ", $new_words);
					
				}
				
				$new_paragraphs[] = implode("\t", $new_tabs);
				
			}
			
			
			return implode("\n", $new_paragraphs);		
			
			
		} else {
			
			
			if(strlen($str) > 1) {
				
				return self::upper(substr($str, 0, 1)).self::lower(substr($str, 1));
				
			} else if(strlen($str) > 0) { 
			
				return self::upper($str);	
				
			} else return '';
			 
			
		}
		
		
		
	}
	
	
	//--------------------------------------------------------------------
	
	
	public static function validate_email($string, $allow_trailing_spaces=true) {
		return (boolean) preg_match('/^(?:[a-zA-Z0-9_\.\-\+]+)\@(?:(?:(?:[a-zA-Z0-9\-])+\.)+(?:[a-zA-Z0-9]{2,4})+)$/', $allow_trailing_spaces ? trim($string) : $string);
	}
	
	
	public static function validate_url($string, $allow_trailing_spaces=true) {
		return (boolean) preg_match('|^(http(s)?://)?[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $allow_trailing_spaces ? trim($string) : $string);
	}
	
	
	//--------------------------------------------------------------------
	
	
	public static function parse_array($string, $array_vars=array(), $separator='') {
		
		$parsed = array();
		foreach(CastHelper::to_array($array_vars) as $vars) $parsed[] = self::parse($string, $vars);
		return implode($separator, $parsed);
	}
	
	
	
	
	public static function parse_query($string) {
		
		$vars = array();
		parse_str($string, $vars);
		return $vars;
	}
	
	
	//--------------------------------------------------------------------
	
	
	public static function uniqid($prefix='') {
		
		$chars = array_map('chr',array_merge(range(48,57), range(97,122)));
		
		$uniqid_chars = str_split(uniqid());
		foreach($uniqid_chars as $index => $char)
			$uniqid_chars[$index] = ArrayHelper::rand_value($chars).$char;
			
		return $prefix.implode('', $uniqid_chars);	
	}
	
	
	
	public static function random($include_letters=true, $include_numbers=true, $max_length=64, $min_length=null) {
		
		
		if($include_letters && $include_numbers) $char_replace_regex = "#[^A-Z0-9]+#";
			
		else if($include_letters) $char_replace_regex = "#[^A-Z]+#";
		
		else if($include_numbers) $char_replace_regex = "#[^0-9]+#";
		
		else return null;
	
		
		$str_length = rand(is_null($min_length) ? $max_length : $min_length, $max_length);
		
		$str = '';
	
		
		while(strlen($str) < $str_length) $str.= preg_replace($char_replace_regex, '', chr(rand(48, 90)));
			
		
		return $str;
		
	}
	
	
	
	public static function escape_geo($str) {
		
		$str = strtolower($str);
		$str = str_replace(array('á', 'e', 'í', 'ó', 'ú', 'ñ'), array('a','e','i','o','u','n'), $str);
		$str = preg_replace('@[^\\w\\s]+@', '', $str);
		$str = preg_replace('@\\s+@', '_', $str);
		
		return $str;
		
	}
	
	
	public static function remove_extended_chars($str) {
	    
	    $str = str_replace(array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'), array('A', 'E', 'I', 'O', 'U', 'N', 'a', 'e', 'i', 'o', 'u', 'ñ'), $str);
	    $str = preg_replace('#[^\w\s]+#', '', $str);
	    return $str;
	    
	}
	

	//--------------------------------------------------------------------
	
	
	public static function camel_case_split($string) {
		
		$string_len = strlen($string);
		
		if($string_len == 0) return array();
		else if($string_len == 1) return array($string);
		
		$parts = array();
		$actual_string = $string{0};
		
		for($i=1; $i<$string_len;$i++) {
			
			$char = $string{$i};
			
			if($char != strtolower($char)) {
				
				if($actual_string != '') {
					$parts[] = $actual_string;
				}
				
				$actual_string = '';
			} 
			
			$actual_string.= $char;
		}
		
		if(strlen($actual_string) > 0) $parts[] = $actual_string;
		
		return $parts;
		
	}
	
	
}
