<?php 


class JSHelper {


	public static function header($charset=null) {
		
		$charset = $charset ? $charset : ZPHP::get_config('charset');
		
		header("Content-Type: text/javascript; charset={$charset}");
	}
	
	//-------------------------------------------------------------------------
	
	public static function escape($str){ 
		return JSHelper::quote($str, '');
	}
	
	
	public static function quote($str, $quote='"'){ 
	
		if(!is_array($str)) {
			return $quote.str_replace(array('\\' ,$quote, "\n"), array('\\\\','\\'.$quote, '\n'), (string) $str).$quote;
		
		} else {
			
			$new_array = array();
			foreach($str as $key=>$value)
				$new_array[$key] = JSHelper::quote($value, $quote);
				
			return $new_array;
		}
	}
	
	
	public static function unescape($str){ 
		if(!is_array($str)) {
			return str_replace(array('\\\\','\"', "\'", '\n'), array('\\' ,'"', "'", "\n"), (string) $var);
		
		} else {
			
			$new_array = array();
			foreach($str as $key=>$value)
				$new_array[$key] = JSHelper::unescape($value);
				
			return $new_array;
		}
	}
	
	//--------------------------------------------------------------------------------------------------
	
	
	public static function cast_number($var=0){
		return str_replace(',', '.', (string) ((float) $var));
	}
	
	
	public static function cast_bool($var=false){
		return $var ? 'true' : 'false';
	}
	
	
	public static function cast_null(){
		return 'null';
	}
	
	
	public static function cast_str($var='', $quote='"'){
		return $quote.str_replace(array('\\' ,$quote, "\n", "\r", "\t"), array('\\\\','\\'.$quote, '\n', '', '\t'), (string) $var).$quote;
	}
	
	
	public static function cast_array($var=array()){
		
		$js_array = array();
		foreach(CastHelper::to_array($var) as $key=>$value) $js_array[] = JSHelper::cast($value); 
		
		return '[' . implode(',', $js_array) . ']';
		
	}
	
	
	public static function cast_obj($var=array()){
		
		$js_obj = array();
		foreach(CastHelper::to_array($var) as $key=>$value) {
			$js_obj[] = JSHelper::cast_str($key) . ':' . JSHelper::cast($value);
		}
		
		return '{' . implode(',', $js_obj) . '}';
	}
	
	
	
	public static function cast($var) {

		if((is_double($var) || is_float($var) || is_int($var) || is_integer($var)) && stripos((string) $var, 'e') === false) return JSHelper::cast_number($var);
		elseif(is_bool($var)) return JSHelper::cast_bool($var);
		elseif(is_null($var)) return JSHelper::cast_null();
		elseif(is_object($var) && method_exists($var, '__toJSON')) return JSHelper::cast($var->__toJSON());
		elseif(is_numeric($var) && strpos($var, '0') !== 0 && stripos((string) $var, 'e') === false) return JSHelper::cast_number($var);
		elseif(is_array($var) || ($to_array = (is_object($var) && method_exists($var, '__toArray')))){
	
			if($to_array) $var = $var->__toArray();
			
			$is_numeric = true;
			$array_keys = array_keys($var);
			
			foreach($array_keys as $index=>$array_key)
				if($array_key !== $index) {
					$is_numeric = false;
					break;
				}
				
			if($is_numeric)	return JSHelper::cast_array($var);
			else return JSHelper::cast_obj($var);		
			
		} else if( ($trimmed_var = trim($var)) && strpos($trimmed_var, '{') === 0 && strrpos($trimmed_var, '}') === strlen($trimmed_var) - 1) 
			return substr($trimmed_var, 1, strlen($trimmed_var) - 2);
			
		else return JSHelper::cast_str($var);
			
		
		
	}
	
	
	//--------------------------------------------------------------------------------------------------
	
	
	
	public static function declare_var($varnames, $value=null, $new=false, $return=false){
		
		$value = trim($value);
		$js_code = '';
		
		if($value) $value = " = {$value}";
		if($new) $varname_prefix = 'var ';
		
		foreach((array) $varnames as $varname)
			$js_code.= "{$varname_prefix}{$varname}{$value};\n";
		
		if($return) return $js_code;
		echo $js_code;
	
	}
	
	//--------------------------------------------------------------------------------------------------
	
	
	public static function call_array($func_name, $args=array(), $quote=false) {
		$args = $quote ? array_map(array(self, 'cast'), (array) $args) : (array) $args;
		return "{$func_name}(".implode(', ', $args).'); ';
	}
	
	
	public static function call_array_quote($func_name, $args=array()) {
		return JSHelper::call_array($func_name, $args, true);
	}
	
	
	public static function call($func_name, $arg1=null, $arg2=null) {
		$func_args = func_get_args();
		return JSHelper::call_array($func_name, array_slice($func_args, 1), false);
	}
	
	public static function call_quote($func_name, $arg1=null, $arg2=null) {
		$func_args = func_get_args();
		return JSHelper::call_array($func_name, array_slice($func_args, 1), true);
	}
	
	
	public static function call_array_out($func_name, $args=array(), $quote=false) {
		
		JSHelper::header();
		
		$func_args = func_get_args();
		echo call_user_func_array(array(self, 'call_array'), $func_args);
	}
	
	
	public static function call_out($func_name, $arg1=null, $arg2=null) {
		
		JSHelper::header();
		
		$func_args = func_get_args();
		echo call_user_func_array(array(self, 'call'), $func_args);
	}
	
	//--------------------------------------------------------------------------------------------------
	
	
	public static function decode_var($value){
		$value = trim($value);
		
		if(strlen($value)==0 || strtolower($value) == 'null') return null;
		else if(preg_match('/^(?:\-)?\d+(?P<float>\.\d+)?$/', $value, $numeric_match)) return $numeric_match['float'] ? ((float) $value) : ((integer) $value);
		else if(in_array(($lower_value=strtolower($value)), array('true','false'))) return $lower_value == 'true';
		else if(preg_match('/^\w+$/', $value)) return $value;
		else if(preg_match('/^(?P<quote>\"|\\\')(?P<str_content>(?:\\\\\\\\|\\\\(\k<quote>)|.|\s)*?)(\k<quote>)$/', $value, $str_match))
			return str_replace(array('\n','\t','\\'.$str_match['quote'],'\\\\'),array("\n","\t",$str_match['quote'],'\\'),$str_match['str_content']);
	
		else if(in_array($value[0], array('[','{')) && $value[strlen($value)-1] == ($value[0]=='[' ? ']' : '}')) {
			
			$error = false;
			$numeric_array = $value[0] == '[';
							
			$array_keys = array();
			$array_values = array();		
			
			$array_str = trim(substr($value, 1, strlen($value)-2), ", \n\t\r");
			$array_str_len = strlen($array_str);
			
			$index = 0;
			
			while(true){
				
				while($index < $array_str_len && in_array($array_str[$index], array("\n", "\r", "\t", ' '))) $index++;
				if($index >= $array_str_len) break;
				
				if($numeric_array) $array_keys[] = count($array_keys);
				else if(preg_match('/\s*(?P<key>(?:(?P<quote>\"|\\\')(?P<str_content>(?:\\\\\\\\|\\\\(\k<quote>)|.|\s)*?)(\k<quote>))|[\w\.\-\_]+)\s*\:\s*/', $array_str, $key_match, PREG_OFFSET_CAPTURE, $index)){
					$array_keys[] = JSHelper::decode_var($key_match['key'][0]);
					$index = $key_match[0][1] + strlen($key_match[0][0]);
										
				} else {
					$error = true;
					break;
				}	
							
				if($array_str[$index] == '{' || $array_str[$index] == '['){
					$value_start = $index;
					$open_delim = $array_str[$index];
					$close_delim = $array_str[$index] == '[' ? ']' : '}';
					$count_opened_delims = 1;
					$index++;
					
					while($index < $array_str_len && $count_opened_delims > 0){
	
						switch($array_str[$index]){
							case $open_delim: $count_opened_delims++; break;
							
							case $close_delim: $count_opened_delims--; break;
							
							case '"': case "'":
								$str_delim = $array_str[$index];
								$index++;
								while($index < $array_str_len && $array_str[$index] != $str_delim){
									if($array_str[$index] == '\\') $index++;
									$index++;
								}							
								
							break;
						
						}
						
						$index++;
						
					}
													
					
					if($count_opened_delims == 0) $array_values[] = JSHelper::decode_var(substr($array_str, $value_start, $index-$value_start));
							
					else {
						$error = true;
						break;
					}
								
				} else if(preg_match('/(?P<value>(?:(?P<quote>\"|\\\')(?P<str_content>(?:\\\\\\\\|\\\\(\k<quote>)|.|\s)*?)(\k<quote>))|[\w\.\-\_]+)\s*/', $array_str, $value_match, PREG_OFFSET_CAPTURE, $index)){
					$array_values[] = JSHelper::decode_var($value_match['value'][0]);
					$index = $value_match[0][1] + strlen($value_match[0][0]);
				
				} else {
					$error = true;
					break;
				}
				
				
				while($index < $array_str_len && in_array($array_str[$index], array("\n", "\r", "\t", ' '))) $index++;
				if($index >= $array_str_len || $array_str[$index] != ',') break;
				
			}
			
			
			if($error) return null;
			else return array_combine($array_keys, $array_values);
			
		} else return null;
		
	}
	
	
	
}
