<?php 

class SQLHelper {
	

	const VALUE_NULL = '@@NULL@@';
	const VALUE_DEFAULT = '@@DEFAULT@@';
	
	//-----------------------------------------------------------------------------------------------------------------------
	

	private static function _escape_string($string) {
		if($string)
		{
//			$escaped = mysql_real_escape_string((string) $string);
			$escaped = mysql_escape_string((string) $string);
			
			return $escaped;
		}
		else
		{
			return '';
		}
	}
	
	public static function escape_bool($string){
		return ((boolean) $string) ? '1' : '0';
	}
	
	
	public static function escape_number($string){
		return (string) ((integer) $string);
	}
	
	
	public static function escape_float($string){
		return (string) ((float) $string);
	}
	
	
	public static function quote($string, $quote="'"){
		
		if(!is_array($string)) {
			
			if(is_null($string) && !is_numeric($string)) return 'NULL';
			
			else {
			
				if(is_bool($string)) {
				
					$quoted = $string ? '1' : '0';
					
				} else {
					
					if(preg_match('#^((\-?[1-9]+[0-9]{0,15})|0)$#', $string)) return $string;
					if(preg_match('#^((\-?[1-9]+(\d+|\.)*\,[0-9]{0,15})|0)$#', $string)) return str_replace(',', '.', str_replace('.', '', $string));
					elseif($string == self::VALUE_NULL) return 'NULL';
					else if($string == self::VALUE_DEFAULT) return 'default';
					else if(is_object($string) && CastHelper::is_instance_of($string, 'Date')) $quoted = $string->format(Date::FORMAT_SQL_DATETIME_SECS);
					else if(is_object($string) && CastHelper::is_instance_of($string, 'Time')) $quoted = $string->format(Time::FORMAT_SQL_TIME_SECS);
					else $quoted = self::_escape_string($string);
				}
			
				return "{$quote}{$quoted}{$quote}";
			}
						
		} else {
			
			$newstrings = array();

			foreach($string as $key=>$value)
				$newstrings[$key] = self::quote($value, $quote);
				
			return $newstrings;
			
		}
	}
	
	
	public static function escape($string){
		return self::quote($string, '');
	}
	
	
	public static function prepare_values_list($values, $quote=true, $close=true){
		
		$values = (array) $values;
		
		if($quote) $values = self::quote($values);
		$sql = implode(', ', $values);
		
		if($close) return sprintf('(%s)', strlen($sql) > 0 ? $sql : 'NULL' );
		else return $sql;
		
	}
	
	
	public static function prepare_columns_values($columns_values, $quote=true, $quote_columns=false){
		
		$columns = array_keys($columns_values);
		$values = array_values($columns_values);
		
		if($quote) $values = self::quote($values);
		if($quote_columns) $columns = self::quote($columns);
		
		$sql_array = array();
		
		foreach($columns as $index=>$column) $sql_array[] = "{$column}={$values[$index]}";
		
		return implode(', ', $sql_array);
	}
	
	
	public static function prepare_insert_values($values, $quote=true, $quote_columns=false) {
		
		$values = CastHelper::to_array($values);
		$num_columns = count($values);
		
		$sql = '';
		if(!ArrayHelper::is_numeric($values)) $sql.= self::prepare_values_list(array_keys($values), $quote_columns, true).' ';
		
		$rows = ArrayHelper::inner_join($values);
		
		if(count($rows) > 0) {
			
			$sql.= 'VALUES ';
			$prepared_rows = array();
			
			foreach($rows as $row) $prepared_rows[] = self::prepare_values_list($row, $quote, true);
			
			$sql.= implode(', ', $prepared_rows);
			
			
		} else $sql.= 'SELECT '.implode(', ', array_fill(0, $num_columns, 'NULL')).' LIMIT 0';
		
		return $sql;
	}
	
	
	//-----------------------------------------------------------------------------------------------------------------------
	
		
	public static function prepare_conditions($conditions=null, $prependWhere=true, $restrict_keys=null, $conditions_join='AND') {
		
		$conditions_tokens = array();
		
		if(!is_null($conditions)) {
			
			$conditions = CastHelper::to_array($conditions);
			
			if(count($conditions) > 0) {
								
				foreach($conditions as $key => $condition) {
					
					if(!is_numeric($key)) {

						if(preg_match('#^(?P<compare>.+?)\|(?P<key>.+?)$#', $key, $key_compare_match)) {
							$key = $key_compare_match['key'];
							$compare_type = str_replace(' ', '', strtolower($key_compare_match['compare']));
						} else {
							$compare_type = '=';
						}
						
						if($restrict_keys) {
							if(!in_array($key, $restrict_keys)) continue;
						}
						
						if(is_array($condition)) {
							if($compare_type == 'between' && count($condition) > 1) $conditions_tokens[] = "`{$key}` BETWEEN ".self::quote($condition[0])." AND ".self::quote($condition[1]);
							else if(($compare_type == '!between' || $compare_type == 'nbetween') && count($condition) > 1) $conditions_tokens[] = "`{$key}` NOT BETWEEN ".self::quote($condition[0])." AND ".self::quote($condition[1]);
							else if($compare_type == 'not') $conditions_tokens[] = "`{$key}` NOT IN ".self::prepare_values_list($condition, true, true);
							else $conditions_tokens[] = "`{$key}` IN ".self::prepare_values_list($condition, true, true);
							
						} else {

							if(in_array($compare_type, array('<', '<=', '>', '>=', '=', '<>', '!='))) {
								
								if($compare_type == '!=') $compare_type = '<>';
								
								if(is_null($condition) && $compare_type == '=') {
									
									$conditions_tokens[] = "`{$key}` IS NULL";
									
								} else if(is_null($condition) && $compare_type == '<>') {
								
									$conditions_tokens[] = "`{$key}` IS NOT NULL";
									
								} else {
									
									$conditions_tokens[] = "`{$key}` ".$compare_type." ".self::quote($condition);
									
								}
								
								
							} else if($compare_type == 'like') {	
								
								$conditions_tokens[] = "`{$key}` LIKE ".self::quote(str_replace('  ', ' ', trim($condition)));
								
							} else if($compare_type == '%like') {		
								
								$conditions_tokens[] = "`{$key}` LIKE '%".self::_escape_string(str_replace('  ', ' ', trim($condition)))."'";
								
							} else if($compare_type == 'like%') {		
								
								$conditions_tokens[] = "`{$key}` LIKE '".self::_escape_string(str_replace('  ', ' ', trim($condition)))."%'";	
								
							} else if($compare_type == '%like%') {		
								
								$conditions_tokens[] = "`{$key}` LIKE '%".self::_escape_string(str_replace('  ', ' ', trim($condition)))."%'";		
								
							} else if($compare_type == 'nlike' || $compare_type == '!like') {	
								
								$conditions_tokens[] = "`{$key}` NOT LIKE ".self::quote(str_replace('  ', ' ', trim($condition)));
								
							} else if($compare_type == '%nlike' || $compare_type == '%!like' || $compare_type == 'n%like' || $compare_type == '!%like') {		
								
								$conditions_tokens[] = "`{$key}` NOT LIKE '%".self::_escape_string(str_replace('  ', ' ', trim($condition)))."'";
								
							} else if($compare_type == 'nlike%' || $compare_type == '!like%') {		
								
								$conditions_tokens[] = "`{$key}` NOT LIKE '".self::_escape_string(str_replace('  ', ' ', trim($condition)))."%'";	
								
							} else if($compare_type == '%nlike%' || $compare_type == '%!like%' || $compare_type == 'n%like%' || $compare_type == '!%like%') {		
								
								$conditions_tokens[] = "`{$key}` NOT LIKE '%".self::_escape_string(str_replace('  ', ' ', trim($condition)))."%'";			
								
							} else if($compare_type == 'md5') {	
								
								$conditions_tokens[] = "MD5(`{$key}`) = ".self::quote(str_replace('  ', ' ', trim($condition)));

							} else if($compare_type == 'sha1') {

								$conditions_tokens[] = "SHA1(`{$key}`) = ".self::quote(str_replace('  ', ' ', trim($condition)));
								
							} else if($compare_type == 'not') {	
								
								if(is_null($condition)) {
									
									$conditions_tokens[] = "`{$key}` IS NOT NULL";
									
								} else {
									
									$conditions_tokens[] = "`{$key}` <> ".self::quote(str_replace('  ', ' ', trim($condition)));	
								}

							} else if($compare_type == 'search_text' || $compare_type == 'searchtext' || $compare_type == 'search') {		
								
								$conditions_tokens[] = "`{$key}` LIKE '%".self::_escape_string(SQLHelper::prepare_full_text_search($condition))."%'";		

							} else if($compare_type == 'search_words' || $compare_type == 'searchwords' || $compare_type == 'words' || $compare_type == 'search_word' || $compare_type == 'searchword') {

								$text = SQLHelper::prepare_full_text_search($condition);
								$conditions_words = [];

								foreach(explode(' ', $text) as $word)
								{
									$conditions_words[] = "`{$key}` LIKE '%".self::_escape_string($word)."%'";
								}

								if(!empty($conditions_words))
								{
									$conditions_tokens[] = '('.implode(' AND ', $conditions_words).')';
								}

							} else if($compare_type == 'search_words_all' || $compare_type == 'searchwordsall' || $compare_type == 'words_all' || $compare_type == 'search_word_all' || $compare_type == 'searchwordall') {

								$text = SQLHelper::prepare_full_text_search($condition);
								$conditions_words = [];

								foreach(explode(' ', $text) as $word)
								{
									$conditions_words[] = "`{$key}` LIKE '%".self::_escape_string($word)."%'";
								}

								if(!empty($conditions_words))
								{
									$conditions_tokens[] = '('.implode(' OR ', $conditions_words).')';
								}

							} else {
								
								if(is_null($condition)) {
									
									$conditions_tokens[] = "`{$key}` IS NULL";
									
								} else {
								
									$conditions_tokens[] = "`{$key}` = ".self::quote($condition);
									
								}
								
							}
						}
						
					} else {
					
						if(is_array($condition)) {
						
							$sub_conditions = self::prepare_conditions((array) $condition, false, $restrict_keys, 'OR');
							if($sub_conditions) $conditions_tokens[] = '('.$sub_conditions.')';
							
						} else {
							
							$conditions_tokens[] = $condition;
						}
					}
				}
			}
		}
		
		$conditions_tokens = array_filter($conditions_tokens);
		
		if(count($conditions_tokens) > 0) {

			$sql = "(" . implode(') '.$conditions_join.' (', $conditions_tokens).')';
			
			if($prependWhere) $sql = " WHERE {$sql}";

			return $sql;
			
		} else {
			
			return '';
			
		}
		
	}

	
	//-----------------------------------------------------------------------------------------------------------------------
	
	
	protected static function _full_text_search_dict_sort($value1, $value2) {
		return strlen($value1) > strlen($value2) ? -1 : 1;
	}
	
	protected static function _get_full_text_search_dict() {
		
		$dict = array();
		
		$zframework_dict_path = ZPHP::get_config('zframework_dir').'/resources/sql/fulltextsearch.dict.ini';
		
		if(file_exists($zframework_dict_path)) {
			$dict = parse_ini_file($zframework_dict_path);
		}
		
		$user_dict_path = ZPHP::get_config('db_full_text_search_dict_file');
		
		if(file_exists($user_dict_path)) {
			$dict = array_merge($dict, parse_ini_file($user_dict_path));
		}

		uksort($dict, array(self, '_full_text_search_dict_sort'));

		$dict['�'] = 'a';
		$dict['�'] = 'e';
		$dict['�'] = 'i';
		$dict['�'] = 'o';
		$dict['�'] = 'u';
		$dict['�'] = 'n';
		$dict['�'] = 'u';

		return $dict;
		
	}
	
	
	public static function prepare_full_text_search($arg1, $arg2=null) {
		
		$text = '';
		$args = func_get_args();
		
		foreach($args as $arg) {
			if(is_array($arg)) $arg = implode(' ', $arg);
			$text.= ' '.$arg;
		}

		
		$dict = self::_get_full_text_search_dict();
		
		$text = strtolower(trim($text));
		$text = str_ireplace(array_keys($dict), array_values($dict), $text);
		
		$text = preg_replace('#\s+#', ' ', trim($text));
		$text = preg_replace('#[^\w\s]+#', '', $text);
		
		return $text;
		
	}
	

}