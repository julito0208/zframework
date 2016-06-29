<?php

abstract class HTMLControl extends MVParamsContentControl implements MIMEControl, HTMLControlLibraryInterface {

	
	protected static $_global_js_files = array();
	protected static $_global_end_js_files = array();
	protected static $_global_ajax_js_files = array();
	protected static $_global_css_files = array();

	protected static $_css_file_pattern = '/^(?i)\s*(?P<file>\S+)(?:\s+\(\s*media\s*\:\s*(?P<media>\S+)\s*\))?\s*$/';
	
	protected static $_include_css_files_js_function = "function(e){var t=[];var n=document.getElementsByTagName('link');var r=document.getElementsByTagName('head')[0];for(var i=0;i<n.length;i++){var s=n[i];var o=s.getAttribute('rel');if(o&&o=='stylesheet'){t.push(s.getAttribute('href'))}}for(var i=0;i<e.length;i++){var u=e[i];if(!(t.indexOf(u['file'])>=0)){var a=document.createElement('link');a.setAttribute('type','text/css');a.setAttribute('rel','stylesheet');a.setAttribute('href',u['file']);a.setAttribute('media',u['media']);r.appendChild(a);t.push(u['file'])}}}";
	protected static $_include_js_files_js_function = "function(e,t){var n=document.getElementsByTagName('script');for(var r=0;r<e.length;r++){var i=e[r];var s=false;for(var o=0;o<n.length;o++){var u=n[o];if(u&&u.getAttribute('type')=='text/javascript'&&u.getAttribute('src')==i){s=true;break}}if(!s){var u=document.createElement('script');u.setAttribute('type','text/javascript');u.setAttribute('src',i);if(t){document.getElementsByTagName('body')[0].appendChild(u)}else{document.getElementsByTagName('head')[0].appendChild(u)}}}}";
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected static function _parse_js_files($files){
		
		$args = func_get_args();
		$js_files = array();
		
		foreach($args as $files) {
			
			foreach((array) $files as $file)
			{
				if (!in_array($file, $js_files))
				{
					if(preg_match('#(?i)^\w+\:\/\/#', $file))
					{
						$js_files[] = $file;
					}
					else
					{
						$js_files[] = self::_convert_resource_url($file);
					}

				}

			}
		}
		
		return $js_files;
	}
	
	
	
	protected static function _parse_css_files($files) {
				
		$args = func_get_args();
		$css_files = array();
		
		foreach($args as $files) {
			
			foreach((array) $files as $file)
			{
				if(preg_match(self::$_css_file_pattern, $file, $file_match))
				{
					if(preg_match('#(?i)^\w+\:\/\/#', $file_match['file']))
					{
						$css_files[$file_match['file']] = array('file'=>$file_match['file'], 'media'=>$file_match['media'] ? $file_match['media'] : 'all');
					}
					else
					{
						$css_files[$file_match['file']] = array('file'=>self::_convert_resource_url($file_match['file']), 'media'=>$file_match['media'] ? $file_match['media'] : 'all');
					}


				}

			}

		}
		
		return $css_files;
	}
	
	
	
	protected static function _comment_js_code($html) {
		
		$html = preg_replace("/(?i)(?P<tag>\<script\s+.*?\>)(?!\<\!\-\-)(?:(?:\n|\s)*)/", "\$1<!--\n", $html);
		$html = preg_replace("/(?i)(?<!\-\-\>)(?:(?:\n|\s)*)(?P<tag>\<\/script\>)/", "\n-->\$1", $html);
		$html = preg_replace("/(?i)(?m)(?P<tag_open>\<script\s+.*?\>)(?:\<\!\-\-(?:\s|\n)*\-\-\>)(?P<tag_end>\<\/script\>)/", "\$1\$2", $html);
		$html = preg_replace("/(?i)(?m)((?:\-\-\>).*?(?:\<\/script\>))/", "\$1\n", $html);
		$html = preg_replace("/(?i)(?m)((?:\<script.*?\>).*?\<\!\-\-)/", "\n\$1", $html);
		return $html;
	}

	
	protected static function _replace_tag_content_callback($match) {
		
	}

	protected static function _resource_replace_callback($match)
	{
		return $match['start'].self::_convert_resource_url($match['url']).$match['end'];
	}
	
	protected static function _prepare_html_content($html) {

		if(!ZPHP::is_development_mode()) {

			if (ZPHP::get_config('html_compress')) {

				$html = preg_replace("#\\>(?:(?:\n|\\s)+?)\\<#", '> <', $html);
				$html = preg_replace("#(?m)(?s)\\>(?:(?:\n|\\s) )(.*?)(?:(?:\n|\\s)*)\\<#", '> ${1} <', $html);
				$html = preg_replace('#(?i)(\>)(\<script)#', "\${1}\n\${2}", $html);
				$html = preg_replace('#(?i)(\<\/script\>)(\<\/?\w)#', "\${1}\n\${2}", $html);

			} else {

				$html = preg_replace("#\\>(?:(?:\n|\\s)+?)\\<#", '> <', $html);
			}
		}

		$html = preg_replace_callback('#(?i)(?P<start>(?:src)\s*\=\s*(?:\'|"))(?P<url>.*?)(?P<end>(?:\'|"))#', array('self', '_resource_replace_callback'), $html);
		return $html;
	}
	
	protected static function _css_files_unique($css_files) {
		
		$unique_css_files = array();
		$unique_css_filenames = array();
		
		$css_files = array_filter((array) $css_files);
		
		foreach($css_files as $css_file) {
			if(!in_array($css_file['file'], $unique_css_filenames)) {
				$unique_css_filenames[] = $css_file['file'];
				$unique_css_files[] = $css_file;
			}
		}
		
		return $unique_css_files;
		
	}
	
	/* @return HTMLControlStaticLibrary */
	protected static function _parse_static_library($library) {
		
		if($library && ClassHelper::is_instance_of($library, 'HTMLControlStaticLibrary')) {
		
			return $library;
			
		} else {
			
			return ClassHelper::create_instance($library);
			
		}
		
	}
	
	protected static function _prepare_js_files_array(array $js_files) {

		if(ZPHP::get_config('html_use_min_js')) {
			
			foreach($js_files as $index => $js_file) {

				if(preg_match('#^\w+\:\/\/'.preg_quote(ZPHP::get_config('site_domain').ZPHP::get_config('site_document_path')).'#', $js_file) && !preg_match('#(?i)\.min\.js$#', $js_file) && preg_match('#(?i)\.js$#', $js_file)) {
					
					if(preg_match('#^(?i)(?P<url>.+)\.js$#', $js_file, $match)) {
						
						$js_file = self::_convert_resource_url($match['url'].'.min.js');
						
					}
					
				}
				
				$js_files[$index] = $js_file;
			}
		}

		if(ZPHP::is_development_mode() && ZPHP::get_config('html.force_reload_css_js'))
		{
			foreach($js_files as $index => $js_file)
			{
				$url = new URL($js_file);
				$url->set_use_language(false);
				$url->set_param('_rid', uniqid());
				$js_files[$index] = $url->to_string();
			}
		}

		return $js_files;
	}
			
	protected static function _prepare_css_files_array(array $css_files) {
		
		if(ZPHP::get_config('html_use_min_css')) {
			
			foreach($css_files as $index => $css_file) {
				
				if(preg_match('#^\w+\:\/\/'.preg_quote(ZPHP::get_config('site_domain').ZPHP::get_config('site_document_path')).'#', $css_file['file']) && !preg_match('#(?i)\.min\.css$#', $css_file['file']) && preg_match('#(?i)\.css$#', $css_file['file'])) {
					
					if(preg_match('#^(?i)(?P<url>.+)\.css$#', $css_file['file'], $match)) {
						
						$css_file['file'] = self::_convert_resource_url($match['url'].'.min.css');
						
					}
					
				}
				
				$css_files[$index] = $css_file;
			}
		}

		if(ZPHP::is_development_mode() && ZPHP::get_config('html.force_reload_css_js'))
		{
			foreach($css_files as $index => $css_file)
			{
				$url = new URL($css_file['file']);
				$url->set_use_language(false);
				$url->set_param('_rid', uniqid());

				$css_files[$index]['file'] = $url->to_string();
			}
		}
		
		return $css_files;
	}
			
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public static function add_global_js_files($files){
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		self::$_global_js_files = array_unique(array_merge(self::$_global_js_files, $js_files));
	}
	
	
	
	public static function add_global_css_files($files){
		
		$args = func_get_args();
		$css_files = call_user_func_array(array('self', '_parse_css_files'), $args);
		
		foreach($css_files as $css_file) {
			self::$_global_css_files[] = $css_file;
		}
	}
	
	
	public static function add_global_js_files_zframework($files){
		
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		foreach($js_files as $index => $js_file) {
			
			$js_files[$index] = URLHelper::get_zframework_static_url($js_file);
		}
		
		self::$_global_js_files = array_unique(array_merge(self::$_global_js_files, $js_files));
	}
	
	
	
	public static function add_global_css_files_zframework($files){
		
		$args = func_get_args();
		$css_files = call_user_func_array(array('self', '_parse_css_files'), $args);
		
		foreach($css_files as $index => $css_file) {
			
			$css_file['file'] = URLHelper::get_zframework_static_url($css_file['file']);
			self::$_global_css_files[] = $css_file;
		}
	}
	
	
	public static function add_global_end_js_files_zframework($files){
		
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		foreach($js_files as $index => $js_file) {
			
			$js_files[$index] = URLHelper::get_zframework_static_url($js_file);
		}
		
		self::$_global_end_js_files = array_unique(array_merge(self::$_global_end_js_files, $js_files));
	}
	
	
	public static function add_global_end_js_files($files){
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		self::$_global_end_js_files = array_unique(array_merge(self::$_global_end_js_files, $js_files));
	}

	public static function add_global_ajax_js_files($files){

		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);

		self::$_global_ajax_js_files = array_unique(array_merge(self::$_global_ajax_js_files, $js_files));
	}

	public static function add_global_static_library($library) {
		
		$args = func_get_args();
		
		foreach($args as $arg) {

			$library = self::_parse_static_library($arg);
			
			if($library) {
				
				foreach($library->get_dependence_libraries() as $depend_library) {
					self::add_global_static_library($depend_library);
				}
				
				self::add_global_css_files($library->get_library_css_files());
				self::add_global_js_files($library->get_library_js_files());
				self::add_global_end_js_files($library->get_library_js_end_files());
				self::add_global_ajax_js_files($library->get_library_js_ajax_files());
			}
		}
	}
	
	protected static function _remove_global_css_files()
	{
		self::$_global_css_files = array();
	}
	
	protected static function _remove_global_js_files()
	{
		self::$_global_js_files = array();
		self::$_global_end_js_files = array();
	}
	
	protected static function _remove_global_static_files() 
	{
		self::_remove_global_css_files();
		self::_remove_global_js_files();
	}

	protected static function _convert_resource_url($url, $base_url=null)
	{
		if(!$base_url) $base_url = ZPHP::get_config('site_url');

		$url = trim($url);
		$base_url = trim($base_url, '/');

		if(preg_match('#^\w+\:\/\/.+#', $url) == 1)
		{
			return $url;
		}
		else if(strpos($url, '/') === 0)
		{
			return $base_url.'/'.ltrim ($url, '/');
		}
		else
		{
			return $url;
		}
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected $_charset;
	protected $_mimetype;
	protected $_js_files = array();
	protected $_end_js_files = array();
	protected $_ajax_js_files = array();
	protected $_css_files = array();
	protected $_include_global_static = false;

	public function __construct($params=null) {
		parent::__construct($params);
		$this->set_charset(ZPHP::get_config('html_charset'));
		$this->set_mimetype(ZPHP::get_config('html_mimetype'));
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------

	protected function _prepare_js_files_html(array $js_files) {
		$js_files_array = JSHelper::cast_array($js_files);
		$html = "<script type='text/javascript'><!--\n";
		$html.= "(".self::$_include_js_files_js_function.")(".$js_files_array.", false);";
		$html.= "\n--></script>\n";
		return $html;
	}
	
	protected function _prepare_css_files_html(array $css_files) {
		$css_files_array = JSHelper::cast_array($css_files);
		$html = "<script type='text/javascript'><!--\n";
		$html.= "(".self::$_include_css_files_js_function.")(".$css_files_array.");";
		$html.= "\n--></script>\n";
		return $html;
	}
	
	protected function _prepare_parsed_content($content) {
		
		$html = '';

		$js_files = array_merge($this->_js_files, array());
		$css_files = array_merge($this->_css_files, array());
		
		if($this->_include_global_static && $this->_is_main_parsing_control) {
			$js_files = array_merge($js_files, self::$_global_js_files);
			$css_files = array_merge($css_files, self::$_global_css_files);
		}

		$js_files = self::_prepare_js_files_array(array_filter(array_unique($js_files)));
		$css_files = self::_prepare_css_files_array(self::_css_files_unique($css_files));

		if(!empty($css_files)) {
			$html.= $this->_prepare_css_files_html($css_files);
		}

		if(!empty($js_files)) {
			$html.= $this->_prepare_js_files_html($js_files);
		}
		
		if($this->_include_global_static && $this->_is_main_parsing_control) {
			self::$_global_js_files = array();
			self::$_global_css_files = array();
		}
		
		
		$content = self::_prepare_html_content($content);
		$html.= self::_comment_js_code($content);
		
		$js_files = array_merge($this->_end_js_files, array());
		
		if($this->_include_global_static && $this->_is_main_parsing_control) {
			$js_files = array_merge($js_files, self::$_global_end_js_files);
		}

		$js_files = array_unique(self::_prepare_js_files_array(array_filter(array_unique($js_files))));


		if(!empty($js_files)) {
			$js_files_array = JSHelper::cast_array($js_files);
			$html.= "\n<script type='text/javascript'><!--\n";
			$html.= "(".self::$_include_js_files_js_function.")(".$js_files_array.", false);";
			$html.= "\n--></script>";
		}


		return $html;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function __set($name, $value=null) { 
		if($name == 'html') {
			$this->set_html($value);
		} else {
			return parent::__set($name, $value);
		}
	}
	
	public function __get($name) { 
		if($name == 'html') {
			return $this->get_html();
		} else {
			return parent::__get($name);
		}
	}
	
	public function __unset($name) { 
		if($name == 'html') {
			$this->clear_html();
		} else {
			return parent::__unset($name);
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function add_html($html) {
		$this->add_content($html);
	}
	
	public function get_html() {
		return $this->get_content();
	}

	public function set_html($html) {
		$this->set_content($html);
	}
	
	public function clear_html() {
		$this->clear_content();
	}

	public function get_charset() {
		return $this->_charset;
	}

	public function set_charset($value) {
		$this->_charset = $value;
		return $this;
	}

	public function get_mimetype() {
		return $this->_mimetype;
	}

	public function set_mimetype($value) {
		$this->_mimetype = $value;
		return $this;
	}

	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function add_js_files($files){
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		$this->_js_files = array_unique(array_merge($this->_js_files, $js_files));
	}
	
	
	
	public function add_end_js_files($files){
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);

		$this->_end_js_files = array_unique(array_merge($this->_end_js_files, $js_files));
	}

	public function add_ajax_js_files($files){

		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);

		$this->_ajax_js_files = array_unique(array_merge($this->_ajax_js_files, $js_files));
	}

	
	
	public function add_css_files($files){
		
		$args = func_get_args();
		$css_files = call_user_func_array(array('self', '_parse_css_files'), $args);
		
		foreach($css_files as $css_file) {
			$this->_css_files[] = $css_file;
		}
	}
	
	
	public function add_js_files_zframework($files){
		
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		foreach($js_files as $index => $js_file) {
			
			$js_files[$index] = URLHelper::get_zframework_static_url($js_file);
		}
		
		$this->_js_files = array_unique(array_merge($this->_js_files, $js_files));
	}
	
	public function add_end_js_files_zframework($files){
		
		
		$args = func_get_args();
		$js_files = call_user_func_array(array('self', '_parse_js_files'), $args);
		
		foreach($js_files as $index => $js_file) {
			
			$js_files[$index] = URLHelper::get_zframework_static_url($js_file);
		}
		
		$this->_end_js_files = array_unique(array_merge($this->_end_js_files, $js_files));
	}
	
	
	
	public function add_css_files_zframework($files){
		
		$args = func_get_args();
		$css_files = call_user_func_array(array('self', '_parse_css_files'), $args);
		
		foreach($css_files as $index => $css_file) {
			$css_file['file'] = URLHelper::get_zframework_static_url($css_file['file']);
			$this->_css_files[] = $css_file;
		}
	}
	

	public function add_static_library($library) {
		
		$args = func_get_args();
		
		foreach($args as $arg) {

			$library = self::_parse_static_library($arg);
			
			if($library) {

				foreach($library->get_dependence_libraries() as $depend_library) {
					$this->add_static_library($depend_library);
				}
				
				$this->add_css_files($library->get_library_css_files());
				$this->add_js_files($library->get_library_js_files());
				$this->add_end_js_files($library->get_library_js_end_files());
				$this->add_ajax_js_files($library->get_library_js_ajax_files());
				$library->update_html_control($this);
				
				if(ClassHelper::is_instance_of($this, 'HTMLPage')) {
					$library->update_html_page($this);
				}
			}
		}
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------

	public function out() {

		if(!self::$_is_parsing) {
			@ header("Content-Type: {$this->_mimetype}; charset=\"{$this->_charset}\"");
		}
		
		$content = $this->to_string();
		echo $content;
		
		if(!self::$_is_parsing) {
			exit;
		}
		
	}
	
	public function save_to($filename) {
		@ file_put_contents($filename, $this->to_string());
	}

	public function out_attachment($filename=null) {
		NavigationHelper::header_content_attachment($filename ? $filename : 'html.html');
		$this->out();
	}
}