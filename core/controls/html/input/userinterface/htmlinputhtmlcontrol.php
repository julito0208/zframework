<?php

class HTMLInputHTMLControl extends HTMLInputControl {

	const UPLOAD_IMAGE_MAX_WIDTH = 500;
	const UPLOAD_IMAGE_MAX_HEIGHT = 500;

	public static function ajax_get_image_html() {
		
		$json = new AjaxJSONFormResponse();
		
		if($_FILES && $_FILES['file'] && $_FILES['file']['error'] == 0) {
			
			$image = Image::from_uploaded_file('file');

			if($image) {

				if($image->get_width() > self::UPLOAD_IMAGE_MAX_WIDTH)
				{
					$image->set_width(self::UPLOAD_IMAGE_MAX_WIDTH, true);
				}

				if($image->get_height() > self::UPLOAD_IMAGE_MAX_HEIGHT)
				{
					$image->set_height(self::UPLOAD_IMAGE_MAX_HEIGHT, true);
				}


				$html = new HTMLBlockImageBase64($image);
				
				if($image->get_width() >= self::IMAGE_MAX_INIT_WIDTH) {
					$html->set_width(self::IMAGE_MAX_INIT_WIDTH);
				}
				
				$json->set_success(true);
				$json->set_item('html', $html->to_string());
			}
		}
		
		$json->out();
		
	}
	
	//------------------------------------------------------------------------------
	
	const IMAGE_MAX_INIT_WIDTH = 400;
	
	const TOOLBAR_ITEM_HR = 'hr';
	const TOOLBAR_ITEM_UNDO = 'undo';
	const TOOLBAR_ITEM_REDO = 'redo';
	const TOOLBAR_ITEM_BOLD = 'bold';
	const TOOLBAR_ITEM_ITALIC = 'italic';
	const TOOLBAR_ITEM_UNDERLINE = 'underline';
	const TOOLBAR_ITEM_LINK = 'link';
	const TOOLBAR_ITEM_IMAGE = 'image';
	const TOOLBAR_ITEM_CHARMAP = 'charmap';
	const TOOLBAR_ITEM_PASTE = 'paste';
	const TOOLBAR_ITEM_COPY = 'copy';
	const TOOLBAR_ITEM_CUT = 'cut';
	const TOOLBAR_ITEM_PRINT = 'print';
	const TOOLBAR_ITEM_PREVIEW = 'preview';
	const TOOLBAR_ITEM_ANCHOR = 'anchor';
	const TOOLBAR_ITEM_PAGEBREAK = 'pagebreak';
	const TOOLBAR_ITEM_SPELLCHECKER = 'spellchecker';
	const TOOLBAR_ITEM_SEARCH_REPLACE = 'searchreplace';
	const TOOLBAR_ITEM_VISUAL_BLOCKS = 'visualblocks';
	const TOOLBAR_ITEM_VISUAL_CHARS = 'visualchars';
	const TOOLBAR_ITEM_CODE = 'code';
	const TOOLBAR_ITEM_FULLSCREEN = 'fullscreen';
	const TOOLBAR_ITEM_INSERT_DATETIME = 'insertdatetime';
	const TOOLBAR_ITEM_MEDIA = 'media';
	const TOOLBAR_ITEM_NON_BREAKING = 'nonbreaking';
	const TOOLBAR_ITEM_SAVE = 'save';
	const TOOLBAR_ITEM_TABLE = 'table';
	const TOOLBAR_ITEM_DIRECTIONALITY = 'directionality';
	const TOOLBAR_ITEM_EMOTICONS = 'emoticons';
	const TOOLBAR_ITEM_TEMPLATE = 'template';
	const TOOLBAR_ITEM_FORECOLOR = 'forecolor';
	const TOOLBAR_ITEM_BACKCOLOR = 'backcolor';
	const TOOLBAR_ITEM_ALIGN_LEFT = 'alignleft';
	const TOOLBAR_ITEM_ALIGN_RIGHT = 'alignright';
	const TOOLBAR_ITEM_ALIGN_CENTER = 'aligncenter';
	const TOOLBAR_ITEM_ALIGN_JUSTIFY = 'alignjustify';
	const TOOLBAR_ITEM_BULLLIST = 'bullist';
	const TOOLBAR_ITEM_NUMLIST = 'numlist';
	const TOOLBAR_ITEM_OUTDENT = 'outdent';
	const TOOLBAR_ITEM_INDENT = 'indent';
	const TOOLBAR_ITEM_UNORDERED_LIST = self::TOOLBAR_ITEM_BULLLIST;
	const TOOLBAR_ITEM_ORDERED_LIST = self::TOOLBAR_ITEM_NUMLIST;
	const TOOLBAR_ITEM_FONT_SIZE = 'fontsizeselect';
	const TOOLBAR_ITEM_FONT_FORMAT = 'styleselect';
	const TOOLBAR_ITEM_SEPARATOR = '|';
	const TOOLBAR_ROW = 'row';
		
	const MENUBAR_ITEM_LINK = 'link';
	const MENUBAR_ITEM_IMAGE = 'image';
	const MENUBAR_ITEM_CHARMAP = 'charmap';
	const MENUBAR_ITEM_PASTE = 'paste';
	const MENUBAR_ITEM_PRINT = 'print';
	const MENUBAR_ITEM_PREVIEW = 'preview';
	const MENUBAR_ITEM_HR = 'hr';
	const MENUBAR_ITEM_ANCHOR = 'anchor';
	const MENUBAR_ITEM_PAGEBREAK = 'pagebreak';
	const MENUBAR_ITEM_SPELLCHECKER = 'spellchecker';
	const MENUBAR_ITEM_SEARCH_REPLACE = 'searchreplace';
	const MENUBAR_ITEM_VISUAL_BLOCKS = 'visualblocks';
	const MENUBAR_ITEM_VISUAL_CHARS = 'visualchars';
	const MENUBAR_ITEM_CODE = 'code';
	const MENUBAR_ITEM_FULLSCREEN = 'fullscreen';
	const MENUBAR_ITEM_INSERT_DATETIME = 'insertdatetime';
	const MENUBAR_ITEM_MEDIA = 'media';
	const MENUBAR_ITEM_NON_BREAKING = 'nonbreaking';
	const MENUBAR_ITEM_TABLE = 'table';
	
	const MENUBAR_MENU_FILE = 'file';
	const MENUBAR_MENU_EDIT = 'edit';
	const MENUBAR_MENU_INSERT = 'insert';
	const MENUBAR_MENU_VIEW = 'view';
	const MENUBAR_MENU_FORMAT = 'format';
	const MENUBAR_MENU_TABLE = 'table';
	const MENUBAR_MENU_TOOLS = 'tools';

	const LANGUAGE_ES = 'es';
	const LANGUAGE_EN = 'en';
	const LANGUAGE_FR = 'fr';
	
	const DEFAULT_HEIGHT = 300;
	const DEFAULT_WIDTH = '100%';
	const DEFAULT_LANGUAGE = self::LANGUAGE_ES;
	const DEFAULT_USE_TOOLBAR = true;
	
	const DEFAULT_SHUFFLE_ID = true;
	
	protected static $_language_values = array(
		self::LANGUAGE_EN => 'en_GB',
		self::LANGUAGE_ES => 'es_MX',
		self::LANGUAGE_FR => 'fr_FR',
	);
	
	protected static $_default_toolbar_items = array(
		
		array(
			self::TOOLBAR_ITEM_FONT_FORMAT,
			self::TOOLBAR_ITEM_FONT_SIZE,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_FORECOLOR,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_BACKCOLOR,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_BOLD,
			self::TOOLBAR_ITEM_ITALIC,
			self::TOOLBAR_ITEM_UNDERLINE,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_ALIGN_LEFT,
			self::TOOLBAR_ITEM_ALIGN_CENTER,
			self::TOOLBAR_ITEM_ALIGN_RIGHT,
			self::TOOLBAR_ITEM_ALIGN_JUSTIFY,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_OUTDENT,
			self::TOOLBAR_ITEM_INDENT,
			self::TOOLBAR_ITEM_SEPARATOR,
			self::TOOLBAR_ITEM_TABLE,
			self::TOOLBAR_ITEM_LINK,
			self::TOOLBAR_ITEM_IMAGE,
			'youtube'
		),
		
	);
	
	protected static function _prepare_toolbar($toolbar) {
		
		foreach((array) $toolbar as $row_index => $row) {
			$toolbar[$row_index] = implode(' ', (array) $row);
		}
		
		return $toolbar;		
	}
	
	
	protected static $_theme = 'modern';

	protected $_id;
	protected $_original_id;
	protected $_name;
	protected $_value;
	protected $_toolbar = array();
	protected $_height;
	protected $_width;
	protected $_language;
	protected $_use_default_toolbar = true;
	protected $_use_toolbar = true;
	protected $_shuffle_id;
	protected $_paste_clear_font = true;
	
	public function __construct($id=null) {
		
		parent::__construct();

		self::add_global_static_library(self::STATIC_LIBRARY_TINY_MCE);
		self::add_global_static_library(self::STATIC_LIBRARY_MODAL_DIALOG);
		self::add_global_static_library(self::STATIC_LIBRARY_AJAX_FORM);
		
		$this->set_shuffle_id(self::DEFAULT_SHUFFLE_ID);
		$this->set_id($id);
		$this->set_height(self::DEFAULT_HEIGHT);
		$this->set_width(self::DEFAULT_WIDTH);
		$this->set_language(self::DEFAULT_LANGUAGE);
		$this->set_use_toolbar(self::DEFAULT_USE_TOOLBAR);
	}
	
	protected function _update_id()
	{
		if($this->_shuffle_id)
		{
			$this->_id = $this->_original_id.uniqid();
		}
		else
		{
			$this->_id = $this->_original_id;
		}
	}
	
	public function get_shuffle_id() 
	{
		return $this->_shuffle_id;
	}

	public function set_shuffle_id($value)

	{
		$this->_shuffle_id = $value;
		$this->_update_id();
	}

	public function clear_toolbar() {
		$this->_toolbar = array(array());
	}
	
	public function get_id() {
		return $this->_id;
	}

	public function set_id($value) {
		$this->_original_id = $value;
		$this->_update_id();
		return $this;
	}


	public function get_name() {
		return $this->_name;
	}

	public function set_name($value) {
		$this->_name = $value;
		return $this;
	}


	public function get_value() {
		return $this->_value;
	}

	public function set_value($value) {
		$this->_value = $value;
		return $this;
	}
	
	public function get_language() {
		return $this->_language;
	}

	public function set_language($value) {
		
		$value = trim(strtolower($value));
		
		if(array_key_exists($value, self::$_language_values)) {
			$this->_language = array_key_exists($value, self::$_language_values) ? $value : self::DEFAULT_LANGUAGE;
		}
		
	}

	
	public function add_tool($tool) {
		
		$args = func_get_args();
		
		foreach($args as $arg) {
			
			$tools_str = is_array($arg) ? $arg : array($arg);
			
			foreach($tools_str as $tool_str) {
				
				$tool_str = preg_replace('#\s+', ' ', trim(strtolower($tool_str)));
				
				foreach(explode(' ', $tool_str) as $tool) {

					if($tool == self::TOOLBAR_ROW) {
						$this->_toolbar[] = array();
					} else {
						$this->_toolbar[count($this->_toolbar)-1][] = $tool;
					}
					
					$this->_use_default_toolbar = false;	
				}
				
			}
			
		}
		
	}

	public function get_height() {
		return $this->_height;
	}

	public function set_height($value) {
		$this->_height = $value;
	}
	
	public function get_width() {
		return $this->_width;
	}

	public function set_width($value) {
		$this->_width = $value;
	}

	public function get_use_toolbar() {
		return $this->_use_toolbar;
	}

	public function set_use_toolbar($value) {
		$this->_use_toolbar = $value;
	}

	
	public function prepare_params() {
		
		parent::prepare_params();
		
		$this->set_param('id', $this->_id ? $this->_id : $this->_name);
		$this->set_param('id_container', $this->get_param('id').'-container');
		$this->set_param('name', $this->_name ? $this->_name : $this->_original_id);
		$this->set_param('value', $this->_value);
		$this->set_param('height', $this->_height);
		$this->set_param('width', $this->_width);
		$this->set_param('language', self::$_language_values[$this->_language]);
		$this->set_param('toolbar', $this->_use_toolbar ? self::_prepare_toolbar($this->_use_default_toolbar ? self::$_default_toolbar_items : $this->_toolbar) : false);
		$this->set_param('paste_clear_font', $this->_paste_clear_font);
	}
	
}
