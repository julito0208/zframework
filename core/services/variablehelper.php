<?php 


class VariableHelper {


	public static function var_escape($string){
		
		if(!is_array($string)) 
			return str_replace(array('"', "'", '\\'), array('\"', "\'", '\\\\'), (string) $string);
			
		else {
			
			$newstrings = array();
			foreach($string as $key=>$value)
				$newstrings[$key] = VariableHelper::var_escape($value);
				
			return $newstrings;
		}
		
	}
	
	
	public static function var_unescape($string){
		
		if(!is_array($string)) 
			return str_replace(array('\"', "\'", '\\\\'), array('"', "'", '\\'), (string) $string);
			
		else {
			
			$newstrings = array();
			foreach($string as $key=>$value)
				$newstrings[$key] = VariableHelper::var_unescape($value);
				
			return $newstrings;
		}
		
	}
	
	
	//--------------------------------------------------------------------
	
	public static function gpc_quote(){
		
		if(!$GLOBALS[ZPHP_VARNAME]['quoted_gpc']) {
			$GLOBALS['_GET'] = VariableHelper::var_escape($GLOBALS['_GET']);
			$GLOBALS['_POST'] = VariableHelper::var_escape($GLOBALS['_POST']);
			$GLOBALS['_COOKIE'] = VariableHelper::var_escape($GLOBALS['_COOKIE']);
			$GLOBALS[ZPHP_VARNAME]['quoted_gpc'] = true;
		}	
	}
	
	
	public static function gpc_unquote(){
		
		if($GLOBALS[ZPHP_VARNAME]['quoted_gpc']) {
			$GLOBALS['_GET'] = VariableHelper::var_unescape($GLOBALS['_GET']);
			$GLOBALS['_POST'] = VariableHelper::var_unescape($GLOBALS['_POST']);
			$GLOBALS['_COOKIE'] = VariableHelper::var_unescape($GLOBALS['_COOKIE']);
			$GLOBALS[ZPHP_VARNAME]['quoted_gpc'] = false;
		}	
	}
	
	//--------------------------------------------------------------------
	
	public static function global_var_unique($value=null, $prefix='uniquevar'){
		
		$varname = uniqid($prefix);
		$GLOBALS[$varname] = $value;
		return $varname;
	}
	
	
	public static function global_var_set($varname, $value=null){
		if(is_null($value)) unset($GLOBALS[$varname]);
		else $GLOBALS[$varname] = $value;
	}
	
	
	public static function global_var_get($varname, $unset=false){
		$value = $GLOBALS[$varname];
		if($unset) unset($GLOBALS[$varname]);
		return $value;
	}
	
	
	public static function global_var_unset($varname) {
		VariableHelper::global_var_set($varname, null);
	}
	
	
	public static function global_var($arg1=null, $arg2=null) {
		switch(func_num_args()) {
			case 0: return VariableHelper::global_var_unique(); break;
			case 1: return VariableHelper::global_var_get($arg1); break;
			default: return VariableHelper::global_var_set($arg1, $arg2); break;
		}
	}
	
	
	
	//--------------------------------------------------------------------
	
	public static function get_class_var($class, $varname) {
		
		$class_vars = get_class_vars($class);
		return $class_vars[$varname];	
	}
	
	
	public static function get_class_constant($class, $constant) {
		return constant(str_replace(':', '', (string) $class) . '::' . $constant);
	}
	
	
	//--------------------------------------------------------------------
	
	public static function var_show($var, $name=null, $return=false){
		
		if(is_string($var)) $var_str = "<pre style='color: #000; margin:0; display: inline; font: mono'>\"".HTMLHelper::escape($var)."\"</pre>";
		else $var_str = preg_replace('/'.preg_quote('&lt;?php&nbsp;').'/', '', highlight_string('<?php '.var_export($var, true), true), 1);
			
		if($name) $var_str = "<span style='white-space: pre; font-weight:bold; text-transform:uppercase'>".HTMLHelper::escape($name).":</span> &nbsp; ".$var_str;
		
		$style = "text-align:left; font-size: 9pt; background: #EFEFEF; padding: 10px; border: solid 1px #000; margin: 0px 0px 0px; font-family: monospace";
		$html = "<div style='{$style}'>{$var_str}</div>";	
		
		if($return) return $html;
		else echo $html;
		
	}
	
	
	public static function var_show_die($var, $name=null) {
		VariableHelper::var_show($var, $name, true);
		die();
	}
	
	
	public static function var_export_html($var, $return=false) {
		$html = VariableHelper::var_show($var, null, true);
		
		if($return) return $html;
		else echo $html;
	}
	
	public static function var_dump_html($var, $return=false) {
		
		ob_start();
		var_dump($var);
		$contents = ob_get_clean();
		
		$contents = "<pre style='text-align:left; font-size: 9pt; background: #EFEFEF; padding: 10px; border: solid 1px #000; margin: 0px 0px 0px; font-family: monospace'>".HTMLHelper::escape($contents)."</pre>";
		
		if($return)
		{
			return $contents;
		}
		else
		{
			echo $contents;
		}
	}
	
	
	public static function var_die($var){
		zphp_load_library('navigation');
		NavigationHelper::header_content_text_plain();
		die($var);
	}
	
	public static function var_export_die($var) {
		zphp_load_library('navigation');
		NavigationHelper::header_content_text_plain();
		die(var_export($var,true));
	}
	
	
	
	
	//-------------------------------------------------------------------- 
	
	public static function variables_get_all_parents($classname) {
		
		if(is_object($classname)) $classname = get_class ($classname);
		
		$classes = array();
		
	//	if($include_self) $classes[] = $classname;
		
		while($classname) {
			 $classes[] = $classname;
			$classname = get_parent_class($classname);
		}
		
		
		return $classes;
		
	}
	
	/*--------------------------------------------------------------------*/
	
	public static function dump_debug($string, $append=true, $include_date=true) {
		
		$file = constant('ZPHP_DUMP_DEBUG_FILE');
		
		if($include_date) {
			
			$date_line = "# ".strftime('%Y-%m-%d %H:%M:%S', time())." -----------------------------------------";
			$string = "{$date_line}\n\n{$string}";
			
		}
		
		$string.="\n\n";
		
		if($append) {
			file_append($file, $string);
		} else {
			file_write($file, $string);
		}
		
	}
	
	
	public static function dump_debug_var($var, $append=true, $include_date=true) {
		
		VariableHelper::dump_debug(var_export($var, true), $append, $include_date);
		
	}
	
	public static function dump_debug_clear() {
		VariableHelper::dump_debug('', false);
	}
	
	
	//-------------------------------------------------------------------- 
	
	public static function variable_save_to_file($filename, $variable) {
		$serialized = serialize($variable);
		$result = @ file_put_contents($filename, $serialized);
		return $result === false ? false : true;
	}
	
	public static function variable_load_from_file($filename) {
		$contents = @ file_get_contents($filename);
		return unserialize($contents);
	}
	
	
}
