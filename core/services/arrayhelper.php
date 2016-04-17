<?php //------------------------------------------------------------------------------------------------- 


class ArrayHelper {

	const ARRAY_SORT_ROWS = 1;
	const ARRAY_SORT_COLS = 2;
	
	//------------------------------------------------------------------------------------------------- 

	public static function plain($arg1=null, $arg2=null) {
		
		$list = array();
		
		foreach(func_get_args() as $arg) 
			if(is_array($arg)) $list = array_merge($list, call_user_func_array(array('ArrayHelper', 'plain'), $arg));
			else $list[] = $arg;
			
		return $list;	
	}
	
	
	//------------------------------------------------------------------------------------------------- 
	
	public static function reduce_keys($array, $keys=null){
		
		$newarray = array();
		$array = (array) $array;
		
		$args = func_get_args();
		foreach(array_slice($args, 1 ) as $keys)	
			foreach( ((array) $keys) as $key)
				if(array_key_exists($key, $array))
					$newarray[$key] = $array[$key];
				
		return $newarray;
		
	}
	
	
	
	public static function select_keys($array, $keys, $default=null){
		
		$new_array = array();
		$array = (array) $array;
		
		foreach((array) $keys as $key)
			$new_array[$key] = array_key_exists($key, $array) ? $array[$key] : $default;
			
		return $new_array;
	}
	
	
	
	public static function concat($array1, $array2=array()){
		
		$array = array();
		$args = func_get_args();
		
		foreach($args as $arg)
			$array = array_merge($array, array_values((array) $arg));
			
		return $array;
	}
	
	
	public static function merge_all($array1=null, $array2=null){
		$merged=array(); $args = func_get_args();
		foreach($args as $arg) $merged = array_merge($merged, (array) $arg);
		
		return $merged;	
	}
	
	
	
	public static function update(&$array, $array1=null, $array2=null) {
		$args = func_get_args();
		$array = call_user_func_array('array_merge_all', $args);
	}
	
	
	
	public static function is_numeric($array) {
		
		if(!is_array($array)) return false;
		
		$array_keys = array_keys((array) $array);
		
		foreach($array_keys as $index=>$array_key)
			if($array_key !== $index) return false;
			
		return true;	
	}
	
	
	public static function sprintf($format, $array) {
		
		$formatted = array();
		
		foreach((array) $array as $key => $value)
			$formatted[$key] = sprintf($format, $value);
			
		return $formatted;	
	}
	
	
	
	public static function get_value($array, $keys, $default=null) {
		
		$array = (array) $array;
		
		foreach((array) $keys as $key)
			if(array_key_exists($key, $array)) return $array[$key];
		
		return $default;
	}
	
	
	
	public static function pop_value(&$array, $keys, $default=null) {
		
		$array = (array) $array;
		
		foreach((array) $keys as $key) 		
			if(array_key_exists($key, $array)) {
				$value = $array[$key];
				unset($array[$key]);
				return $value;
			}
		
		
		return $default;
	}
	
	
	//------------------------------------------------------------------------------------------------- 
	
	
	public static function rand_key($array) {
		return array_rand((array) $array);
	}
	
	
	
	
	public static function rand_value(&$array, $delete=false) {
		
		$key = ArrayHelper::rand_key($array);
		$value = $array[$key];
		
		if($delete) unset($array[$key]);
		
		return $value;
	}
	
	
	
	//------------------------------------------------------------------------------------------------- 
	
	
	public static function fill_values($array, $keys, $value=null) {
		
		$filled = (array) $array;
		
		
		if(is_numeric($keys)) {
		
			while(count($filled) < $keys) $filled[] = $value;
			
		} else {
			
			foreach((array) $keys as $key)
				if(!array_key_exists($key, $filled))
					$filled[$key] = $value;
			
		}
		
		return $filled;
	}
	
	
	
	public static function from_keys($keys, $value=null) {
		return ArrayHelper::fill_values(array(), $keys, $value);
	}
	
	
	
	public static function zip($arrays, $fill=null) {
		
		$arrays = (array) $arrays;
		$zipped = array();
		
		if(func_num_args() > 1) {
			
			$max_length = 0;
			foreach($arrays as $array) $max_length = max($max_length, count((array) $array));
			
			$length = $max_length;
		
		} else {
			
			$min_length = -1;
			
			foreach($arrays as $array) 
				$min_length = $min_length == -1 ? count((array) $array) : min($min_length, count((array) $array));
		
		
			$length = $min_length;
			
		}	
				
		for($i=0; $i<$length; $i++) $zipped[] = array();
		
		
		foreach($arrays as $array) {
			
			$array = array_values((array) $array);
			
			if($fill) $array = ArrayHelper::fill_values($array, $length, $fill);
			
			for($i=0; $i<$length; $i++) $zipped[$i][] = $array[$i];		
		}
		
		return $zipped;
		
	}
	
	
	public static function encode_html($array, $prefix=null, $include_numeric_keys=false) {
		
		$array = CastHelper::to_array($array);
		$key_format = $prefix ? ((!$include_numeric_keys && ArrayHelper::is_numeric($array) && !((boolean) array_filter($array, 'is_array'))) ? "${prefix}[]" : "{$prefix}[%s]") : '%s';
		
		$encoded = array();
		
		foreach($array as $key => $value) {
						
			$key_encoded = sprintf($key_format, $key);
			
			if(is_array($value)) $encoded = array_merge($encoded, ArrayHelper::encode_html($value, $key_encoded, $include_numeric_keys));
			
			else $encoded[] = array('name' => $key_encoded, 'value' => $value);
		}
		
			
		return $encoded;
		
	}
	
	
	
	
	public static function http_query($array, $encode_all=true) {
		
		if($encode_all) 
			return http_build_query(CastHelper::to_array($array));
		
		else {
					
			$escape_chars = array( 0 => '%', 1 => '/', 2 => '&', 3 => '=', 4 => '!', 5 => '"', 6 => '·', 7 => '$', 8 => '\\', 9 => '(', 10 => ')', 11 => '?', 12 => '¿', 13 => 'ª', 14 => 'º', 15 => '|', 16 => '@', 17 => '#', 18 => '¡', 19 => '\'', 20 => '<', 21 => '>', 22 => '{', 23 => '}', 24 => '[', 25 => '*', 26 => ']', 27 => '^', 28 => '+', 29 => ':', 30 => ';', 31 => ',', 32 => ' ');
			$escape_replace = array ( 0 => '%25', 1 => '%2F', 2 => '%26', 3 => '%3D', 4 => '%21', 5 => '%22', 6 => '%B7', 7 => '%24', 8 => '%5C', 9 => '%28', 10 => '%29', 11 => '%3F', 12 => '%BF', 13 => '%AA', 14 => '%BA', 15 => '%7C', 16 => '%40', 17 => '%23', 18 => '%A1', 19 => '%27', 20 => '%3C', 21 => '%3E', 22 => '%7B', 23 => '%7D', 24 => '%5B', 25 => '%2A', 26 => '%5D', 27 => '%5E', 28 => '%2B', 29 => '%3A', 30 => '%3B', 31 => '%2C', 32 => '+');
			
			$http_query = array();
			
			foreach(ArrayHelper::encode_html($array, null, true) as $key_value) 
				$http_query[] = str_replace($escape_chars, $escape_replace, $key_value['name']).'='.str_replace($escape_chars, $escape_replace, $key_value['value']);
				
			
			return implode('&', $http_query);
		}
		
		
	}
	
	
	//------------------------------------------------------------------------------------------------- 
	
	public static function inner_join($array) {
		
		if(func_num_args() > 1) {
			$args = func_get_args();
			return ArrayHelper::inner_join(call_user_func_array('array_merge_all', $args));
		
		} else {
		
			$original = CastHelper::to_array($array);
			
			foreach($original as $name => $value)
				
				if(is_array($value)) {
					
					$rows = array();
					
					foreach($value as $array_name => $array_value)
						$rows = array_merge($rows, ArrayHelper::inner_join(array_merge($original, array($name => $array_value))));
					
					return $rows;	
				}
			
				
			return array($original);	
				
		}
	}
	
	
	
	public static function divide($array, $parts_length, $fill=null) {
		
		if($parts_length <= 0) return array();
		
		$array = array_values((array) $array);
		$array_length = count($array);
		
		$num_parts = ceil($array_length / $parts_length);
		
		if($num_parts <= 0) return array();
		
		if(!is_null($fill) && ($num_parts * $parts_length) > $array_length)
			$array = ArrayHelper::fill_values($array, $num_parts * $parts_length, $fill);
			
			
		$parts = array();
	
		for($i=0; $i<$num_parts; $i++) $parts[] = array_slice($array, $i * $parts_length, $parts_length);
		
		return $parts;
	}
	
	
	public static function transpose($array) {
	
		$array = (array) $array;
		$transposed = array();
		
		foreach($array as $row_key => $row) {
			
			$row = (array) $row;
			
			foreach($row as $col_key => $value) {
				
				if(!isset($transposed[$col_key])) $transposed[$col_key] = array();
				$transposed[$col_key][$row_key] = $value;
			}
		}
		
		return $transposed;
	}
	
	
	public static function divide_rows($array, $parts_length, $fill=null, $sort=self::ARRAY_SORT_ROWS) {
		
		if($sort == self::ARRAY_SORT_COLS) {
			
			if($parts_length <= 0) return array();
			
			$array = array_values((array) $array);
			$array_length = count($array);
			
			$num_parts = ceil($array_length / $parts_length);
			
			if($num_parts <= 0) return array();
			
			if(!is_null($fill) && ($num_parts * $parts_length) > $array_length)
				$array = ArrayHelper::fill_values($array, $num_parts * $parts_length, $fill);
				
				
			$parts = array();
		
			for($row_index=0; $row_index<$num_parts; $row_index++) {
				
				$row = array();
				
				$index_offset = $row_index * $parts_length;
				
				for($col_index=0; $col_index<$parts_length; $col_index++) {
					
					$index = $index_offset + $col_index;
					if(isset($array[$index])) $row[] = $array[$index];
				}
				
				$parts[] = $row;
			}
			
			return $parts;
		
		} else return ArrayHelper::divide($array, $parts_length, $fill);
		
	}
	
	
	
	public static function insert_subarray(&$array, $index, $subarray=array()) {
		
		$array = array_values((array) $array);
		$subarray = array_values((array) $subarray);
		array_splice($array, $index, 0, $subarray);
		
	}
	
	
	
	public static function insert_value(&$array, $index, $value1=null, $value2=null) {
		
		$args = func_get_args();
		return ArrayHelper::insert_subarray($array, $index, array_slice($args, 2));
	}
	
	
	
	//------------------------------------------------------------------------------------------------- 
	
	
	public static function extended_set_value(&$array, $keys, $value=null) {
		
		$variable_code = '$array';
		$keys = array_values((array) $keys);
		
		foreach($keys as $index => $key) {
			
			$variable_code.= '['.(is_int($key) ? "{$key}" : var_export((string) $key, true) ).']';
			
			if($index < count($keys) - 1) {
				
				eval("if(!isset({$variable_code}) || !is_array({$variable_code})) {$variable_code} = array(); ");
			
			} else {
				
				eval("{$variable_code} = \$value;");
				
			}
		}
		
	}
	
	
	
	
	public static function extended_get_value(&$array, $keys, $default=null, $remove=false) {
		
		$variable_code = '$array';
		$keys = array_values((array) $keys);
		
		foreach($keys as $index => $key) {
			
			$variable_code.= '['.(is_int($key) ? "{$key}" : var_export((string) $key, true) ).']';
			
			if($index < count($keys) - 1) {
				
				eval("if(!isset({$variable_code}) || !is_array({$variable_code})) {$variable_code} = array(); ");
			
			} else {
				
				eval("\$return_value = isset({$variable_code}) ? {$variable_code} : \$default;");
				
				if($remove) eval("unset({$variable_code});");
				
			}
		}
		
		return $return_value;
	}
	
	
	public static function extended_remove_value(&$array, $keys, $default=null) {
		return ArrayHelper::extended_get_value($array, $keys, $default, true);
	}
	
	
	public static function extended_has_key(&$array, $keys) {
		
		$default = uniqid('array', true);
		
		$value = ArrayHelper::extended_get_value($array, $keys, $default);
		
		return $default != $value;
	}
	
	//-------------------------------------------------------------------------------------------------
	
	
	public static function find_value($array, $find_value=null, $strict=false, $default=null) {
		
		foreach((array) $array as $key => $value) {
			
			if($strict && $find_value === $value) return $key;
			else if($find_value == $value) return $key;
		}
		
		
		return $default;
				
		
	}
	
	
	//------------------------------------------------------------------------------------------------- 
	
	
	public static function filter_null($array) {
		
		$new_array = array();
		
		foreach((array) $array as $key => $value) {
			
			if(!is_null($value)) {
				
				if(is_array($value)) {
					
					$value = ArrayHelper::filter_null($value);
					
					if(count($value) > 0) $new_array[$key] = $value;
					
				} else {
					
					$new_array[$key] = $value;
					
				}
				
			}
			
		}
		
		return $new_array;
		
	}
	
	
	//------------------------------------------------------------------------------------------------- 
	
	public static function remove_prefixs($strings, &$prefix=null) {
		
		if(count($strings) == 0) {
			
			return array();
			
		} else {
			
			$prefix = $strings[0];
			
			for($i=1; $i<count($strings); $i++) {
				
				$test_string = $strings[$i];
				$new_prefix = '';
				
				for($j=0; $j<min(strlen($test_string), strlen($prefix)); $j++) {
					
					$test_string_char = $test_string{$j};
					$prefix_char = $prefix{$j};
					
					if($prefix_char == $test_string_char) {
						$new_prefix.= $prefix_char;
					} else {
						break;
					}
					
				}
				
				$prefix = $new_prefix;
				
			}
			
			foreach($strings as $index => $string) {
				$strings[$index] = StringHelper::remove_prefix($string, $prefix);
			}
			
			return $strings;
			
		}
	}
	
	
	public static function remove_sufixs($strings, &$sufix=null) {
		
		foreach($strings as $index => $string) {
			$strings[$index] = strrev($string);
		}
		
		$strings = ArrayHelper::remove_prefixs($strings, $prefix);
		
		foreach($strings as $index => $string) {
			$strings[$index] = strrev($string);
		}
		
		$sufix = strrev($prefix);
		
		return $strings;
	}
	

	//------------------------------------------------------------------------------------------------- 
	
	public static function get_item($array, $key, $default=null)
	{
		$array = (array) $array;
		
		if(array_key_exists($key, $array))
		{
			return $array[$key];
		}
		else
		{
			return $default;
		}
	}
	
}
