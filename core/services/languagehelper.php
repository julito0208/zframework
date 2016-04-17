<?php 

class LanguageHelper  {

	const SECTION_KEY_SEPARATOR = '.';
	const DEFAULT_TEXT = '';

	private static $_flag_classname_varname = 'flag_class';
	
	private static $_name_classname_varname = 'language_name';
	
	private static $_max_language_section_get_include_count = 5;
	
	private static $_current_language = null;
	
	private static $_languages_data = array();
	
	private static $_available_languages = null;

	private static $_current_language_session_varname = '__current_language__';

	/*--------------------------------------------*/
	
	protected static function _table_to_entity($table) {
		
		$table = trim(strtolower($table));
		$parts = array();
		
		foreach(explode('_', $table) as $part) {
			$parts[] = ucfirst($part);
		}
		
		return implode('', $parts);
	}
	
	protected static function _get_language_entity() {
		return self::_table_to_entity(ZPHP::get_config('multi_language_tables_languages_table'));
	}
	
	protected static function _get_language_section_entity() {
		return self::_table_to_entity(ZPHP::get_config('multi_language_tables_languages_section_table'));
	}
	
	protected static function _get_language_text_entity() {
		return self::_table_to_entity(ZPHP::get_config('multi_language_tables_languages_text_table'));
	}
	
	
	/*--------------------------------------------*/
	
	protected static function _get_language_texts($language) {
		
		$entity = self::_get_language_text_entity();
		
		$texts = array();
			
		try {

			eval('$texts = '.$entity.'::list_all(array("id_language" => $language));');

		} catch(Exception $ex) {}
		
		$data = array();
		
		foreach($texts as $text) {
			
			if(!array_key_exists($text->get_id_language_section(), $data)) {
				$data[$text->get_id_language_section()] = array();
			}
			
			$data[$text->get_id_language_section()][$text->get_id_language_text()] = $text->get_text();
		}
		
		return $data;
	}
	
	
	
	protected static function _get_language_texts_section($language, $section) {
		
		$language_texts = null;
		
		if(!array_key_exists($language, self::$_languages_data)) {
			
			$language_texts = self::_get_language_texts($language);
			
			self::$_languages_data[$language] = array();
			
			self::$_languages_data[$language]['sections_names'] = array_keys($language_texts);
			
			self::$_languages_data[$language]['sections_get_count'] = array();
			
			foreach(self::$_languages_data[$language]['sections_names'] as $section_name) {
				self::$_languages_data[$language]['sections_get_count'][$section_name] = 0;
			}
			
			self::$_languages_data[$language]['sections_texts'] = array();
			
			foreach(self::$_languages_data[$language]['sections_names'] as $section_name) {
				self::$_languages_data[$language]['sections_texts'][$section_name] = null;
			}
		}
		
		if(!is_null(self::$_languages_data[$language]['sections_texts'][$section])) {
			
			return self::$_languages_data[$language]['sections_texts'][$section];
			
		} else {
			
			if(is_null($language_texts)) {
				$language_texts = self::_get_language_texts($language);
			}
			
			self::$_languages_data[$language]['sections_get_count'][$section]++;
			
			if(self::$_languages_data[$language]['sections_get_count'][$section] > self::$_max_language_section_get_include_count) {
				self::$_languages_data[$language]['sections_texts'][$section] = $language_texts[$section];
			}
			
			return $language_texts[$section];
			
		}
		
	}
	
	
	protected static function _get_language_texts_sections_names($language) {
	
		
		$language_texts = null;
		
		if(!array_key_exists($language, self::$_languages_data)) {
			
			$language_texts = self::_get_language_texts($language);
			
			self::$_languages_data[$language] = array();
			
			self::$_languages_data[$language]['sections_names'] = array_keys($language_texts);
			
			self::$_languages_data[$language]['sections_get_count'] = array();
			
			foreach(self::$_languages_data[$language]['sections_names'] as $section_name) {
				self::$_languages_data[$language]['sections_get_count'][$section_name] = 0;
			}
			
			self::$_languages_data[$language]['sections_texts'] = array();
			
			foreach(self::$_languages_data[$language]['sections_names'] as $section_name) {
				self::$_languages_data[$language]['sections_texts'][$section_name] = null;
			}
		}
		
		return self::$_languages_data[$language]['sections_names'];
	}
	
	
	protected static function _parse_section_text_key($var, $language) {
		
		if(is_array($var) && count($var) > 1) {
			
			return self::_parse_section_text_key($var[0].self::SECTION_KEY_SEPARATOR.$var[1], $language);
			
		} else if(is_array($var)) {
			
			return self::_parse_section_text_key($var[0], $language);
			
		} else {
			
			$key = null;
			$section = null;			
			
			$var = preg_replace('#(\s+|[^0-9a-zA-Z\.\_\-]+)+#', '', strtolower((string) $var));
			
			if(strpos($var, self::SECTION_KEY_SEPARATOR) !== false) {
				
				list($section, $key) = explode(self::SECTION_KEY_SEPARATOR, $var, 2);
				
			} else {
				
				$section = ZPHP::get_config('multi_language_default_section');
				$key = $var;
			}
			
		}
		
		return array($section, $key);
	}


   
	protected static $_default_language_texts = array();
	
    protected static function _parse_default_language_ini_file($language)
    {
        $language = preg_replace('#[^a-zA-Z0-9]+#', '', $language);
		
		if(!array_key_exists($language, self::$_default_language_texts)) {

			$language_texts = array();
			$ini_files = array();

			foreach((array) ZPHP::get_config('multi_language.files_format') as $file_format)
			{
				$filename = sprintf($file_format, $language);

				if(file_exists($filename))
				{
					$ini_files[] = $filename;
				}
			}

			$ini_files[] = ZPHP::get_zframework_dir()."/resources/languages/{$language}.ini";

			foreach($ini_files as $ini_file)
			{
				@ $language_ini_contents = file_get_contents($ini_file);

				foreach (explode("\n", $language_ini_contents) as $line)
				{
					$line = trim($line);

					if (!$line || strpos($line, '=') === false)
						continue;

					list($key, $value) = explode('=', $line, 2);

					$key = trim($key);
					$value = trim($value);

					$language_texts[$key] = $value;
				}
			}

			self::$_default_language_texts[$language] = $language_texts;
		}
		
        return self::$_default_language_texts[$language];

    }  


    protected static function _get_default_text($language, $key, $default=self::DEFAULT_TEXT)
    {
        $language_texts = self::_parse_default_language_ini_file($language);

		if(array_key_exists($key, $language_texts))
		{
			return $language_texts[$key];
		}

        return $default;
    }

	protected static function _update_current_language($language)
	{
		SessionHelper::add_var(self::$_current_language_session_varname, $language);
		self::$_current_language = $language;
	}
		
	/*--------------------------------------------*/
	
	public static function translate_default_texts($language_from, $language_to)
	{

		$language_from = preg_replace('#[^a-zA-Z0-9]+#', '', self::parse_language_code($language_from));
		$language_to = preg_replace('#[^a-zA-Z0-9]+#', '', self::parse_language_code($language_to));
		
		$from_texts = self::_parse_default_language_ini_file($language_from);
		
		$language_to_ini_file = ZPHP::get_zframework_dir()."/resources/languages/{$language_to}.ini";
		@ file_put_contents($language_to_ini_file, '');
		
		foreach($from_texts as $key => $value)
		{
			$value_trans = GoogleTranslate::translate_text($value, $language_from, $language_to);
			$line="{$key}={$value_trans}\n";
			
			file_put_contents($language_to_ini_file, $line, FILE_APPEND);
		}
	}
	
	/*--------------------------------------------*/

	public static function initialize() {
	
		self::set_current_language_from_url();
	}
	
	
	public static function is_enabled() {
		
		return (bool) ZPHP::get_config('multi_language_enabled');
	}
	
	public static function get_id_language_code_from_id_language($id_language)
	{
		$parts = explode('-', $id_language);
		return $parts[0];
	}
	
	public static function get_available_languages() {
		
		if(is_null(self::$_available_languages)) {
			
			if(self::is_enabled())
			{
				$available_languages = array();
				$available_languages[] = self::get_default_language();
				
				$languages = explode(',', ZPHP::get_config('multi_language_languages'));
				
				foreach($languages as $language)
				{
					$available_languages[] = self::parse_language($language);
				}
				
				
				self::$_available_languages = array_unique($available_languages);
			}
			else
			{
				self::$_available_languages = array(self::get_current_language());
			}
			
		}
		
		return self::$_available_languages;
	}
	
	public static function get_available_languages_codes() {
		
		$id_languages = self::get_available_languages();
		
		foreach($id_languages as $index => $id_language)
		{
			$id_languages[$index] = self::get_id_language_code_from_id_language($id_language);
		}
		
		return $id_languages;
		
	}
	
	public static function get_current_language() {

		if(is_null(self::$_current_language)) {

			if(SessionHelper::has_var(self::$_current_language_session_varname))
			{
				$language = SessionHelper::get_var(self::$_current_language_session_varname);
			}
			else
			{
				$language = self::get_default_language();
			}

			self::_update_current_language($language);

		}
		
		return self::$_current_language;
		
	}
	
	public static function get_current_language_code() {
		
		$id_language = self::get_current_language();
		return self::get_id_language_code_from_id_language($id_language);
		
	}
	
	public static function get_default_language() {


		$default_language = ZPHP::get_config('multi_language_default_language');

		if(!$default_language)
		{
			$entity = self::_get_language_entity();
			$languages = array();

			try {

				eval('$languages = '.$entity.'::list_all(array("is_default" => true), "id_language");');

			} catch(Exception $ex) {}

			if(empty($languages)) {

				return ZPHP::get_config('multi_language_default_language');

			} else {

				return $languages[0]->get_id_language();
			}
		}
		else
		{
			return self::parse_language($default_language);
		}
		
	}
	
	public static function get_default_language_code() {
		
		$id_language = self::get_default_language();
		return self::get_id_language_code_from_id_language($id_language);
		
	}

	public static function parse_language($language, $return_current=true) {

		$language = trim(strtolower($language));

		$language_parts = explode('-', $language);
		$language = $language_parts[0];
		
		if(!$language || (!is_null(self::$_available_languages) && !in_array($language, self::get_available_languages_codes())))	{
			
			if($return_current) {
				$language = self::get_current_language();
			} else {
				$language = null;
			}
		}
		
		if(count($language_parts) > 1)
		{
			return strtolower($language).'-'.strtoupper($language_parts[1]);
		}
		else
		{
			return $language;
		}
	}

	public static function parse_language_code($language, $return_current=true) {

		$id_language = self::parse_language($language, $return_current);

		if($id_language)
		{
			return self::get_id_language_code_from_id_language($id_language);
		}
		else
		{
			return null;
		}
	}

	public static function set_current_language($language) {

		self::_update_current_language(self::parse_language_code($language));
	}
	
	
	public static function set_current_language_from_url($url=null) {
		
		if(is_null($url)) $url = ZPHP::get_actual_uri();
		
		list($url, $language) = self::parse_language_url($url);
		
		self::set_current_language($language);
		
	}
	
	
	public static function parse_language_url($url = null) {
		
		if(is_null($url)) $url = ZPHP::get_actual_uri();
		
		$site_url = ZPHP::get_site_url(false);
		$url = preg_replace('#^[\w]+\:\/\/#', '', $url);
		
		if(stripos($url, $site_url) === 0)
		{
			$url = substr($url, strlen($site_url));
		}
		
		$url_format = ZPHP::get_config('multi_language_url_language_format');
		
		$language_group_pattern_array = array();
		
		foreach(self::get_available_languages_codes() as $language) {
			$language_group_pattern_array[] = preg_quote($language);
		}
		
		$language_group_pattern = "(?P<language>(".implode('|', $language_group_pattern_array)."))";
		$language_group_replace = '@@@@-----LANGUAGE@@@@';
		$language_group_replace_quoted = preg_quote($language_group_replace);
		
		$url_group_pattern = "(?P<url>.*)";
		$url_group_replace = '@@@@----URL@@@@';
		$url_group_replace_quoted = preg_quote($url_group_replace);
		
		$url_format_replaced = sprintf($url_format, $language_group_replace, $url_group_replace);
		$url_format_quoted = preg_quote($url_format_replaced);

		$url_pattern = str_replace($language_group_replace_quoted, $language_group_pattern, $url_format_quoted);
		$url_pattern = str_replace($url_group_replace_quoted, $url_group_pattern, $url_pattern);
		$url_pattern = "(?i){$url_pattern}";
		
		$url_pattern = "#^{$url_pattern}$#";
		
		if(preg_match($url_pattern, $url, $match)) {
			$language = self::parse_language_code($match['language']);
			$url = str_replace('//', '/', $match['url']);
		} else {
			$language = null;
		}
		
		if(!$language && rtrim($url, '/') == $url) 
		{
			return self::parse_language_url(rtrim($url, '/').'/');
		}
		
		return array($url, $language);
				
	}
	
	/*--------------------------------------------*/
	
	
	public static function set_text($language, $key, $text, $javascript=false) {
		
		$language = self::parse_language_code($language);
		$language = ZfLanguage::get_row(array('id_language_code' => $language))->get_id_language();

		list($section, $key) = self::_parse_section_text_key($key, $language);
		
		$entity = self::_get_language_text_entity();

		try {

			eval('$language_text = '.$entity.'::get_by_id_language_text_id_language_section_id_language($key, $section, $language);');
			if(!$language_text) {
				
				eval('$language_text = new '.$entity.'();');
				$language_text->set_id_language_text($key);
				$language_text->set_id_language_section($section);
				$language_text->set_id_language($language);
			} 

			$language_text->set_javascript($javascript);
			$language_text->set_text($text);
			
			eval($entity.'::save($language_text);');

		} catch(Exception $ex) {}
		
	}
	
	
	
	public static function get_text($key=null, $language=null, $default=self::DEFAULT_TEXT) {
		
		$language = self::parse_language_code($language);

		list($section, $key) = self::_parse_section_text_key($key, $language);
		
		$entity = self::_get_language_text_entity();
			
		$language_text = null;

		try {
//			eval('$language_text = '.$entity.'::get_by_id_language_text_id_language_section_id_language($key, $section, array($language, ZfLanguage::get_by_id_language_code($language)->get_id_language()));');
			eval('$language_text = '.$entity.'::get_by_id_language_text_id_language_section_id_language($key, $section, ZfLanguage::get_by_id_language_code($language)->get_id_language());');

		} catch(Exception $ex) {}

		if($language_text) {
			return $language_text->get_text();
		} else {
			return self::_get_default_text($language, $key, $default);
		}
	}

	
	public static function get_flag_classname($language=null)
	{
		return self::get_text(self::$_flag_classname_varname, $language);
	}
	
	public static function get_language_name($language=null)
	{
		return self::get_text(self::$_name_classname_varname, $language);
	}
	
	
	public static function get_text_all_languages($key=null, $default=self::DEFAULT_TEXT)
	{
		$languages = self::get_available_languages_codes();
		$texts = array();
		
		foreach($languages as $language)
		{
			$texts[$language] = self::get_text($key, $language, $default);
		}
		
		return $texts;
		
	}
	
	
	public static function get_url($url=null, $language=null) {

		if(!self::is_enabled()) return $url;
		
		list($url, $url_language) = self::parse_language_url($url);

		if(!$language) {
			
			$language = $url_language;
			
			if(!$language)
			{
				$language = self::get_current_language_code();
			}
			
		} else {
			
			$language = self::parse_language_code($language);
		}

		$url_format = ZPHP::get_config('multi_language_url_language_format');
		
		$language_url = sprintf($url_format, $language, $url);
		
		$language_url = str_replace('//', '/', $language_url);
		
		$language_url = NavigationHelper::conv_abs_url($language_url);
		
		return rtrim(NavigationHelper::conv_abs_url($language_url), '/');
		
	}
	
	
	public static function get_actual_url($language=null)
	{
		return self::get_url(null, $language);
	}
	
	
	public static function get_language_texts($language=null) {

		$language = self::parse_language_code($language);
		return self::_get_language_texts($language);
		
	}

	/*--------------------------------------------*/
	
	public static function get_language_by_id_language($id_language) {

		$entity = self::_get_language_entity();
			
		$language = null;
		
		try {

			eval('$language = '.$entity.'::get_by_id_language($id_language);');

		} catch(Exception $ex) {}
		
		return $language;
		
	}
	
	public static function list_language_sections($conditions=null, $order=null, $limit=null) {

		$entity = self::_get_language_section_entity();
			
		$language_sections = array();

		try {

			if(class_exists($entity))
			{
				eval('$language_sections = ' . $entity . '::list_all($conditions, $order, $limit);');
			}


		} catch(Exception $ex) {}
		
		return $language_sections;
		
	}
	
	public static function list_language_texts($conditions=null, $order=null, $limit=null) {

		$entity = self::_get_language_text_entity();
			
		$language_texts = array();
		
		try {

			eval('$language_texts = '.$entity.'::list_all($conditions, $order, $limit);');

		} catch(Exception $ex) {}
		
		return $language_texts;
		
	}
	
	public static function has_language_section($id_language_section) {

		$entity = self::_get_language_section_entity();
			
		$language_section = null;
		
		try {

			eval('$language_section = '.$entity.'::get_by_id_language_section($id_language_section);');

		} catch(Exception $ex) {}
		
		return $language_section ? true : false;
		
	}
	
	public static function insert_language_section($id_language_section, $system=true, $user=false) {

		$entity = self::_get_language_section_entity();
			
		$id_language_section = preg_replace('#[^A-Za-z0-9\-\_]+#', '', $id_language_section);
		
		try {

			eval('$language_section = new '.$entity.'();');

		} catch(Exception $ex) {}
		
		if($entity) {
			
			$language_section->set_id_language_section($id_language_section);
			$language_section->set_system($system);
			$language_section->set_user($user);
			
			try {

				eval($entity.'::save($language_section);');
				return $id_language_section;

			} catch(Exception $ex) {
				
				return false;
				
			}

		} else {
			
			return false;
		}
		
	}
	
	public static function delete_language_section($id_language_section) {

		$entity_section = self::_get_language_section_entity();
		$entity_text = self::_get_language_text_entity();

		try {

			eval($entity_text.'::delete_by_id_language_section($id_language_section);');
			eval($entity_section.'::delete_by_id_language_section($id_language_section);');

		} catch(Exception $ex) {}
		
		return true;
		
	}
	
	
	public static function has_language_text($id_language_section, $id_language_text) {

		$entity_section = self::_get_language_section_entity();
		$entity_text = self::_get_language_text_entity();
			
		$language_texts = array();
		
		try {

			eval('$language_texts = '.$entity_text.'::list_all(array("id_language_section" => $id_language_section, "id_language_text" => $id_language_text));');

		} catch(Exception $ex) {}
		
		return !empty($language_texts) ? true : false;
		
	}
	
	
	public static function insert_language_text($id_language_section, $id_language_text, $text='') {

		$entity = self::_get_language_text_entity();
		$languages = self::get_available_languages_codes();
		
		$id_language_text = preg_replace('#[^A-Za-z0-9\-\_]+#', '', $id_language_text);

		try {

			foreach($languages as $id_language) {

				$languages_ids = ZfLanguage::list_all(array('id_language_code' => $id_language));

				foreach($languages_ids as $language) {

					$id_language = $language->id_language;

					eval('$language_text = new ' . $entity . '();');
					eval('$language_text->set_id_language_section($id_language_section);');
					eval('$language_text->set_id_language_text($id_language_text);');
					eval('$language_text->set_id_language($id_language);');
					eval($entity . '::save($language_text);');
				}
			}
			
			return $id_language_text;

		} catch(Exception $ex) {
			return false;
			
		}
	}
	
	
	public static function delete_language_text($id_language_section, $id_language_text) {

		$entity_text = self::_get_language_text_entity();
		$languages = self::get_available_languages();

		try {

			foreach($languages as $id_language)
			{
				$id_language = ZfLanguage::get_row(array('id_language_code' => $id_language))->get_id_language();
				eval($entity_text.'::delete_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language);');
			}

		} catch(Exception $ex) {}
		
		return true;
		
	}
	
	public static function get_javascript_texts($language=null)
	{
		$language = self::parse_language_code($language);
		$javascript_texts = array();
		
		$sections = self::list_language_sections();
		
		foreach($sections as $section)
		{
			$texts = self::list_language_texts(array('id_language' => $language, 'id_language_section' => $section->get_id_language_section(), 'javascript' => true));
			
			if(!empty($texts))
			{
				$javascript_texts[$section->get_id_language_section()] = array();
				
				foreach($texts as $text)
				{
					$javascript_texts[$section->get_id_language_section()][$text->get_id_language_text()] = $text->get_text();
				}
				
			}
		}
		
		$id_language_section = ZPHP::get_config('multi_language_default_section');
		
		if(!array_key_exists($id_language_section, $javascript_texts))
		{
			$javascript_texts[$id_language_section] = array();
		}
		
		$default_texts = self::_parse_default_language_ini_file($language);
		
		foreach($default_texts as $key => $text)
		{
			$javascript_texts[$id_language_section][$key] = $text;
		}
		
		return $javascript_texts;
		
	}
}


