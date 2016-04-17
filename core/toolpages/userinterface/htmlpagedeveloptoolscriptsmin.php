<?php 

class HTMLPageDevelopToolScriptsMin extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/scripts\-?min(?:\.php)?';
	
	protected static $_init_dirs = array();
	protected static $_prefix_init_dirs = '';
	
	protected static function _search_files($dirs, $extension) {
		
		$extension = strtolower($extension);
		$tree = array();
		
		foreach((array) $dirs as $dirname) {
		
			$dirname = rtrim($dirname, '/');
			$dirname = rtrim($dirname, '\\');
			
			$files = FilesHelper::dir_list($dirname, true);

			foreach($files as $filename) {
				
				if(is_dir($filename)) {
					
					$dir_tree = self::_search_files($filename, $extension);
					
					if(!empty($dir_tree)) {
						$tree[] = array('type' => 'dir', 'path' => $filename, 'filename' => preg_replace('#(?i)^.*\/(.+?)$#', '$1', (string) $filename), 'contents' => $dir_tree, 'name' => StringHelper::remove_prefix($filename, self::$_prefix_init_dirs, true), 'extension' => $extension);
					}
					
				} else {
					
					if(StringHelper::ends_with($filename, '.'.$extension, true) && !StringHelper::ends_with($filename, 'min.'.$extension, true)) {
						$tree[] = array('type' => 'file', 'path' => $filename, 'filename' => preg_replace('#(?i)^.*\/(.+?)$#', '$1', (string) $filename), 'name' => StringHelper::remove_prefix($filename, self::$_prefix_init_dirs, true), 'extension' => $extension);
					}
					
				}
				
			}
			
		}		
		
		return $tree;
	}
	
	protected static function _get_dirs_tree($filename) {
		
		$dirnames = array();
		
		$dirname = dirname($filename);
		
		while($dirname != '/' && $dirname != '.' && $dirname != '') {
			
			$dirnames[] = $dirname;
			$dirname = dirname($dirname);
			
		}
		
		
		return $dirnames;
	}
	
	protected static function _mimify_js_file($path_in, $path_out) {
		
		@ include_once(ZPHP::get_third_party_path('/jsmin/jsmin.php'));
		
		@ $js_contents = file_get_contents($path_in);
		
		$min = JSMin::minify($js_contents);
		
		@ file_put_contents($path_out, $min);
	}
	
	protected static function _mimify_css_file($path_in, $path_out) {
		
		@ $css_contents = file_get_contents($path_in);

//		@ include_once(ZPHP::get_third_party_path('/cssmin/cssmin.php'));
//		$min = CssMin::minify($css_contents);

		$min = str_replace("\n\n", "\n", $css_contents);
		
		@ file_put_contents($path_out, $min);
	}
	
	protected static function _get_mimified_file($path, &$extension=null) {
		
		if(preg_match('#(?i)(?P<filename>.*)\.(?P<extension>\w+?)$#', $path, $match)) {
			
			$extension = strtolower($match['extension']);
			
			return $match['filename'].'.min.'.$extension;
			
		} else {
			
			return null;
			
		}
	}
	
	protected static function _mimify_file($path) {
		
		$json = new AjaxJSONFormResponse();
		
		$json->set_success(true);
		$json->set_item('path', $path);
		
		$mimified_file = self::_get_mimified_file($path, $extension);
		
		$json->set_item('mimified_path', $mimified_file, $extension);
		$json->set_item('extension', $extension);

		if($extension == 'js') {
		
			self::_mimify_js_file($path, $mimified_file);
			
		} else if($extension == 'css') {
			
			self::_mimify_css_file($path, $mimified_file);
		}
		
		$json->out();
		
	}
	
	protected static function _delete_file($path) {
		
		$json = new AjaxJSONFormResponse();
		
		$json->set_success(true);
		$json->set_item('path', $path);
		
		$mimified_file = self::_get_mimified_file($path, $extension);
		
		$json->set_item('mimified_path', $mimified_file, $extension);
		$json->set_item('extension', $extension);
		
		@ unlink($mimified_file);
		
		$json->out();
		
	}
	
	
	/*-----------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Scripts Min Tool';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	/*-----------------------------------------*/
	
	
	protected $_acss_files = array();
	protected $_ajs_files = array();
	
	protected $_selected_js_files;
	protected $_selected_css_files;
	
	protected $_opened_css_dirs = array();
	protected $_opened_js_dirs = array();
	
	protected $_success_msg = false;
	protected $_delete_msg = false;
	
	public function __construct() {
		
		parent::__construct();
		
		if($_POST['action']) {
			
			if($_POST['action'] == 'mimify') {
				self::_mimify_file($_POST['file']);
			} else if($_POST['action'] == 'delete') {
				self::_delete_file($_POST['file']);
			}
			
			exit;
		}
		
		self::$_init_dirs = array(
			ZPHP::get_config('www_dir'),
			ZPHP::get_config('backend_dir'),
			ZPHP::get_config('zframework_dir'),
		);

		self::$_prefix_init_dirs = array(
			ZPHP::get_site_dir(),
			dirname(ZPHP::get_site_dir()),
		);

		self::add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_MODAL_DIALOG);
		
		$this->_ajs_files = self::_search_files(self::$_init_dirs, 'js');
		$this->_acss_files = self::_search_files(self::$_init_dirs, 'css');
		
		$selected_files = (array) $_POST['selected_files'];
		
		$this->_selected_css_files = (array) $selected_files['css'];
		$this->_selected_js_files = (array) $selected_files['js'];
		
		$this->_opened_css_dirs = array();
		$this->_opened_js_dirs = array();
		
		foreach($this->_selected_js_files as $js_file) {
			$this->_opened_js_dirs = array_merge($this->_opened_js_dirs, self::_get_dirs_tree($js_file));
		}
		
		foreach($this->_selected_css_files as $css_file) {
			$this->_opened_css_dirs = array_merge($this->_opened_css_dirs, self::_get_dirs_tree($css_file));
		}
		
		if($_POST['selected_action']) {
			
			if($_POST['selected_action'] == 'mimify') {
				$this->_success_msg = !empty($this->_selected_css_files) || !empty($this->_selected_js_files);
			} else if($_POST['selected_action'] == 'delete') {
				$this->_delete_msg = !empty($this->_selected_css_files) || !empty($this->_selected_js_files);
			}
		}
		
		
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('js_files', $this->_ajs_files);
		$this->set_param('css_files', $this->_acss_files);
		$this->set_param('selected_css_files', $this->_selected_css_files);
		$this->set_param('selected_js_files', $this->_selected_js_files);
		$this->set_param('opened_js_dirs', $this->_opened_js_dirs);
		$this->set_param('opened_css_dirs', $this->_opened_css_dirs);
		$this->set_param('success_msg', $this->_success_msg);
		$this->set_param('delete_msg', $this->_delete_msg);
	}
}
