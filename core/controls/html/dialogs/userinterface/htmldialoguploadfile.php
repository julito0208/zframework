<?php

class HTMLDialogUploadFile extends HTMLDialog implements RedirectURLPattern {
	
	const URL_ID = 'UploadFileDialog';
	const DEFAULT_NAME = 'file';
	const DEFAULT_TITLE = 'Archivo';
	const DEFAULT_ACCEPT_LABEL = 'Aceptar';
	const DEFAULT_CANCEL_LABEL = 'Cancelar';
	const DEFAULT_LOADING_LABEL = 'Cargando...';
	
	protected static $_URL_PATTERN = '/fileuploaddialog';
	
	/* @return URLPattern */
	public static function get_url_pattern() {
		return new URLPattern(self::$_URL_PATTERN, self::URL_ID, get_class());
	}
	
	
	/*-----------------------------------------------------------------------------*/
	
	public function __construct() {
		
		parent::__construct();
		
		self::add_global_css_files_zframework('css/controls/uploadfiledialog.css');
		self::add_global_static_library(HTMLControlStaticLibraryAjaxForm);
		
		$submit_on_change = HTTPPost::get_item('submit_on_change', 1);
		$form_id = HTTPPost::get_item('form_id', uniqid());
		$file_id = HTTPPost::get_item('file_id', uniqid());
		$file_name = HTTPPost::get_item('file_name', self::DEFAULT_NAME);
		$file_label = HTTPPost::get_item('label', LanguageHelper::get_text('main.select_file'));
		$accept_label = LanguageHelper::get_text('main.accept', null, self::DEFAULT_ACCEPT_LABEL);
		$cancel_label = LanguageHelper::get_text('main.cancel', null, self::DEFAULT_CANCEL_LABEL);
		$loading_label = LanguageHelper::get_text('main.loading', null, self::DEFAULT_LOADING_LABEL);
		$form_action = HTTPPost::get_item('form_action', 'javascript:void(0)');
		$multiple = HTTPPost::get_item('multiple', 0);
		$callback = HTTPPost::get_item('callback', '');
		$data = HTTPPost::get_item_array('data', array());
		$help_bottom = HTTPPost::get_item('help_bottom', '');
		
		$file_input = new HTMLInputFileControl();
		$file_input->set_id($file_id);
		$file_input->set_name($file_name);
		$file_input->set_title($file_label);
		$file_input->set_label($file_label);
		$file_input->set_show_label_title(true);
		$file_input->set_multiple($multiple);
		
		if($submit_on_change) {
			$file_input->set_on_change("\$('#{$form_id}').submit()");
		}
		
		$this->set_param('form_id', $form_id);
		$this->set_param('accept_label', $accept_label);
		$this->set_param('cancel_label', $cancel_label);
		$this->set_param('loading_label', $loading_label);
		$this->set_param('form_action', $form_action);
		$this->set_param('callback', $callback);
		$this->set_param('data', (array) $data);
		$this->set_param('file_input', $file_input);
		$this->set_param('help_bottom', $help_bottom);
		
	}

}