<?php

abstract class MVControl {

	protected static $_template_this_varname = '_this';
	protected static $_template_content_varname = 'content';
	protected static $_template_classname_varname = 'classname';

	protected static $_is_parsing = false;
	protected static $_parsing_classname = null;
	
	/*---------------------------------------------------------------------------------*/
	
	protected $_parse_all_parents_templates = false;
	protected $_content = '';
	protected $_is_main_parsing_control = false;

	public function __construct($params=null) {}
	
	/*---------------------------------------------------------------------------------*/
	
	protected function _set_parse_all_parents_templates($value) {
		$this->_parse_all_parents_templates = $value;
	}
	
	protected function _get_parse_all_parents_templates() {
		return $this->_parse_all_parents_templates;
	}
	
	/*---------------------------------------------------------------------------------*/
	
	protected function _get_mv_control_templates($get_all=null) {
		
		$get_all = is_null($get_all) ? $this->_parse_all_parents_templates : $get_all;
		$templates = array();

		$classname = get_class($this);

		while($classname) {

			if(is_subclass_of($classname, 'MVControl')) {

				$class_path = ClassHelper::get_class_path($classname);

				if($class_path) {

					$template_paths = [];
					
					$filename = basename($class_path);
					$dirname = dirname($class_path);

					$dirname_basename = basename($dirname);

					if(in_array($dirname_basename, ['userinterface', 'controller', 'controllers'])) {

						$dirname_dirname = dirname($dirname);

						$template_paths[] = $dirname_dirname.'/templates/'.$filename;
						$template_paths[] = $dirname_dirname.'/views/'.$filename;
						$template_paths[] = $dirname_dirname.'/view/'.$filename;
					}
					
					if(empty($template_paths)) {
						
						if(StringHelper::ends_with($filename, '.userinterface.php', true) || StringHelper::ends_with($filename, '.control.php', true)) {
							
							$name = StringHelper::remove_sufix($filename, '.userinterface.php', true);
							$name = StringHelper::remove_sufix($name, '.control.php', true);
							
							$template_path = $dirname."/{$name}.template.php";
							
							if(!file_exists($template_path)) {
								$template_paths[] = $dirname."/{$name}.html.php";
							}
							
						}
					}

					$found_template = false;
					
					if(!empty($template_paths)) {

						foreach($template_paths as $template_path)
						{
							if(file_exists($template_path))
							{
								array_unshift($templates, $template_path);
								$found_template = true;
								break;
							}
						}

						if($found_template && !$get_all)
						{
							break;
						}

					}
				}

				$classname = get_parent_class($classname);

			} else {

				break;

			}
		}
		
		return $templates;
	}

	
	protected function _get_mv_control_template() {
		
		$templates = $this->_get_mv_control_templates(false);
		
		return empty($templates) ? null : $templates[0];
	}
	
	
	/*----------------------------------------------------------------------------------*/
	

	protected function _parse_mv_template($vars=null, $content=null, $template_path=null) {

		$template_path = $template_path ? $template_path : $this->_get_mv_control_template();
		
		$vars = is_null($vars) ? $this->_get_parse_vars() : $vars;
		$vars = CastHelper::to_array($vars);
		$vars[self::$_template_this_varname] = $this;
		$vars[self::$_template_content_varname] = is_null($content) ? $this->_get_parse_content() : $content;
		$vars[self::$_template_classname_varname] = get_class($this);

		$__TEMPLATE_VARS = $vars;
		$__TEMPLATE_PATH = $template_path;
		
		unset($vars);
		unset($template_path);
		
		extract($__TEMPLATE_VARS);

		
		try {
			ob_start();
			include($__TEMPLATE_PATH);
			$__TEMPLATE_CONTENT = ob_get_clean();
		} catch(Exc $ex) {
			ob_clean();
			throw $ex;
		}
		
		return $__TEMPLATE_CONTENT;
	}
	
	protected function _parse_mv_content($vars=null, $content=null, $get_all=null) {
		
		$content = is_null($content) ? $this->_get_parse_content() : $content;
		
		$vars = is_null($vars) ? $this->_get_parse_vars() : $vars;
		$vars = CastHelper::to_array($vars);
		$vars[self::$_template_content_varname] = $content;
		
		$templates = $this->_get_mv_control_templates($get_all);
		$templates = array_reverse($templates);
		
		foreach($templates as $template) {
			$content = $this->_parse_mv_template($vars, $content, $template);
			$vars[self::$_template_content_varname] = $content;
		}
		
		return $content;
	}
	
	/*----------------------------------------------------------------------------------*/
	
	protected function _get_parse_vars() {
		return array();
	}
	
	protected function _get_parse_content() {
		return $this->_content;
	}
	
	protected function _prepare_parsed_content($content) {
		return $content;
	}
	
	/*----------------------------------------------------------------------------------*/
	
	public function to_string() {

		try
		{

			$is_parsing = false;
			$this->_is_main_parsing_control = false;

			if (!self::$_is_parsing)
			{
				self::$_is_parsing = true;
				$this->_is_main_parsing_control = true;
				$is_parsing = true;
			}

			$content = $this->_parse_mv_content();
			$content = $this->_prepare_parsed_content($content);

			if ($is_parsing)
			{
				self::$_is_parsing = false;
			}

			$this->_is_main_parsing_control = false;
		}
		catch(Exception $ex)
		{
			return $ex->getMessage();
		}
		
		return $content;
	}
	
	public function __toString() {
		return $this->to_string();
	}
}