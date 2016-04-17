<?php 

class HTMLPageDevelopToolLanguageTexts extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/languages(?:\.php)?';
	
	public static function ajax_add_section() {
		
		$json = new AjaxJSONFormResponse();
		
		$href = $_POST['href'];
		$id_language_section = $_POST['section'];
		
		if(LanguageHelper::has_language_section($id_language_section)) {
			
			$json->set_error('Section exists');
			
		} else {

			$id_language_section = LanguageHelper::insert_language_section($id_language_section);
			
			$url = NavigationHelper::make_url_query(array('section' => $id_language_section), $href, false);
			
			$json->set_item('url', $url);
			$json->set_success(true);
		}
		
		$json->out();
		
	}

	public static function ajax_add_text() {
		
		$json = new AjaxJSONFormResponse();
		
		$href = $_POST['href'];
		$id_language_section = $_POST['section'];
		$id_language_text = $_POST['text'];
		
		if(LanguageHelper::has_language_text($id_language_section, $id_language_text)) {
			
			$json->set_error('Text exists');
			
		} else {

//			var_export($_POST['texts_languages']);
//			die();
			$id_language_text = LanguageHelper::insert_language_text($id_language_section, $id_language_text);
			
			$url = NavigationHelper::make_url_query(array('section' => $id_language_section, 'language_text' => $id_language_text), $href);
			
			$json->set_item('url', $url);
			$json->set_success(true);
		}
		
		$json->out();
		
	}
	
	public static function ajax_delete_section() {
		
		$json = new AjaxJSONFormResponse();
		
		$id_language_section = $_POST['section'];
		
		if(!LanguageHelper::has_language_section($id_language_section)) {
			
			$json->set_error('Section not exists');
			
		} else {

			LanguageHelper::delete_language_section($id_language_section);
			$json->set_success(true);
		}
		
		$json->out();
		
	}
	
	
	public static function ajax_delete_text() {
		
		$json = new AjaxJSONFormResponse();
		
		$id_language_text = $_POST['text'];
		$id_language_section = $_POST['section'];
		
		if(!LanguageHelper::has_language_text($id_language_section, $id_language_text)) {
			
			$json->set_error('Text not exists');
			
		} else {

			LanguageHelper::delete_language_text($id_language_section, $id_language_text);
			$json->set_success(true);
		}
		
		$json->out();
		
	}
	
	/*-----------------------------------------*/
	
	protected static function _get_title()
	{
		return 'Languages Texts Generation';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	/*-----------------------------------------*/
	
	protected $_languages;
	protected $_sections_data;
	protected $_selected_section;
	protected $_selected_language_text;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_MODAL_DIALOG);
		
		if($_POST['submit']) {

			$texts_javascripts = (array) $_POST['texts_javascripts'];
			
			foreach((array) $_POST['texts_languages'] as $id_language => $texts1) {
				
				foreach((array) $texts1 as $id_language_section => $texts2) {
					
					foreach((array) $texts2 as $id_language_text => $text) { 

						if(array_key_exists($id_language_section, $texts_javascripts) && array_key_exists($id_language_text, $texts_javascripts[$id_language_section]))
						{
							$javascript = $texts_javascripts[$id_language_section][$id_language_text];
						}
						else
						{
							$javascript = false;
						}
						
						LanguageHelper::set_text($id_language, "{$id_language_section}.{$id_language_text}", $text, $javascript);
						
					}
					
				}
			}
			
		}
		
		if(HTTPPost::exists('section')) {
			
			$this->_selected_section = HTTPPost::get_item('section');
			$this->_selected_language_text = HTTPPost::get_item('language_text');
			
		} else {
			
			$this->_selected_section = HTTPGet::get_item('section');
			$this->_selected_language_text = HTTPGet::get_item('language_text');
		}
		
		
		$id_languages = LanguageHelper::get_available_languages_codes();
		$this->_languages = array();
		
		foreach($id_languages as $id_language) {
			$this->_languages[] = LanguageHelper::get_language_by_id_language($id_language);
		}
		
		$this->_sections_data = array();
		
		$language_sections = LanguageHelper::list_language_sections(array('system' => true, 'user' => false), 'id_language_section');
		
		$languages = LanguageHelper::get_available_languages_codes();
		
		foreach($language_sections as $language_section) {
			
			$section_data = array();
			$section_data['section'] = $language_section;
			$section_data['texts'] = array();
			
			$id_language_texts = array();
			$texts = LanguageHelper::list_language_texts(array('id_language_section' => $language_section->get_id_language_section()), 'id_language_text');
			
			foreach($texts as $text) {
				$id_language_texts[] = $text->get_id_language_text();
			}
			
			$id_language_texts = array_unique($id_language_texts);
			sort($id_language_texts);
			
			$section_data['texts_languages'] = array();
			
			foreach($id_language_texts as $id_language_text) {

				$section_data['texts_languages'][$id_language_text] = array('texts' => array(), 'javascript' => false);
				
				foreach($languages as $id_language) {

					$texts = LanguageHelper::list_language_texts(array('id_language' => ZfLanguage::get_row(array('id_language_code' => $id_language))->get_id_language(), 'id_language_section' => $language_section->get_id_language_section(), 'id_language_text' => $id_language_text), 'id_language_text');

					if(!empty($texts)) {
						
						$text = $texts[0]->get_text();
						$section_data['texts_languages'][$id_language_text]['javascript'] = $texts[0]->get_javascript();
						
					} else {
						
						$text = '';
						
					}


					$input = new HTMLInputTextAreaControl();
					$input->set_class('language-text-input');
					$input->set_id(uniqid());
					$input->set_attrs(array('id-section' => $language_section->get_id_language_section(), 'id-language-text' => $id_language_text));
					$input->set_title($id_language_text);
					$input->set_show_label_title(true);
					$input->set_id(uniqid());
					$input->set_value($text);
					$input->set_label($id_language_text." <span class='language'>[".$id_language."]</span>");
					$input->set_disabled($language_section->get_id_language_section() != $this->_selected_section);
					$input->set_name("texts_languages[{$id_language}][{$language_section->get_id_language_section()}][{$id_language_text}]");

					$section_data['texts_languages'][$id_language_text]['texts'][$id_language] = array('input' => $input, 'text' => $text);

				}

			}
			
			$this->_sections_data[] = $section_data;
		}
		
		
	}
	
	public function prepare_params() {
		
		parent::prepare_params();
		$this->set_param('languages', $this->_languages);
		$this->set_param('sections_data', $this->_sections_data);
		$this->set_param('selected_section', $this->_selected_section);
		$this->set_param('selected_language_text', $this->_selected_language_text);
	}
}