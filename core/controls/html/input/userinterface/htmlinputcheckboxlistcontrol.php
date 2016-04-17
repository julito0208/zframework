<?php //-----------------------------------------------------------------------


class HTMLInputCheckboxListControl extends HTMLInputControl {
	
	protected $_options = array();
	
	
	public function __construct($id=null, $name=null, array $options=array()) {
				
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_options($options);
		
	}
	
	
	//-----------------------------------------------------------------------

	public function get_options() {
		return $this->_options;
	}
	
	
	public function add_option($option_label, $value='') {
		
		if(func_num_args() == 2) {
			
			$this->_options[] = array('label' => $option_label, 'value' => $value, 'checked' => false);
			
		} else {
			
			if($option_label && $option_label instanceof OptionItem) {
				
				$this->_options[] = array('label' => $option_label->get_option_item_label(), 'value' => $option_label->get_option_item_value(), 'checked' => false);
				
			} else {
				
				if($option_label instanceof DBEntity) {
					
					$primary_keys = $option_label->primary_keys;
					$fields = $option_label->fields;
					
					if(count($primary_keys) == 1 && in_array('nombre', $fields)) {
						
						$option = array('label' => $option_label->nombre, 'value' => $option_label->$primary_keys[0]);
						$this->_options[] = $option;
						
						return $this;
					}
					
				}
				
				$option = CastHelper::to_array($option_label);
				
				$option_value = $option['value'];
				$option_label = '';

				foreach(array('text', 'label', 'title', 'html') as $key) {

					if($option[$key]) {

						$option_label = $option[$key];
						break;
					}

				}

				$this->_options[] = array('label' => $option_label, 'value' => $option_value, 'checked' => false);
				
			}
			
		}

		return $this;
		
	}

	
	public function clear_options() {
		$this->_options = array();
		return $this;
	}
	
	
	public function add_options(array $options) {
		
		foreach((array) $options as $option) {
			$this->add_option($option);
		}
		
		return $this;
	}
	
	public function set_options(array $options) {
		
		$this->clear_options();
		$this->add_options($options);
		return $this;
	}

	
	public function set_option_checked($value, $checked) {
		
		if(is_array($value)) {
			
			foreach($value as $v) {
				$this->set_option_checked($v, $checked);
			}
			
			return $this;
			
		}
		
		if($value && $value instanceof OptionItem) $value = $value->get_option_item_value();
		
		foreach($this->_options as $index => $option) {
			
			if($option['value'] == $value) {
				
				$this->_options[$index]['checked'] = $checked;
				break;
			}
		}
		
		return $this;
		
	}
	
	
	public function set_selected($value) {
		
		$args = func_get_args();
		
		foreach($args as $arg) $this->set_option_checked($arg, true);
		
		return true;
		
	}
	
	
	public function clear_selected() {
		
		foreach($this->_options as $index => $option) {
			$this->_options[$index]['checked'] = false;
		}
		
		return $this;
		
	}
	
	
	public function get_selected() {
		
		$values = array();
		
		foreach($this->_options as $index => $option) {
			
			if($option['checked']) {
				$values[] = $option['value'];
			}
		}
		
		return $values;
		
	}
	
	public function set_value($value) {
		
		return $this->set_selected($value);
	}
	
	
	public function get_value() {
		
		return $this->get_selected();
	}
	

	//-----------------------------------------------------------------------
	
	
	public function prepare_params() {
		
		$inputs = array();
		
		foreach($this->_options as $index => $option) {
			$option = (array) $option;
			$input = new HTMLInputCheckboxControl($this->get_id().'_'.$option['value'].'_'.$index, $this->get_name().'[]');
			$input->set_title($option['label']);
			$input->set_value($option['value']);
			$input->set_checked($option['checked']);
			
			$inputs[] = $input;
			
		}
		
		$this->set_param('inputs', $inputs);

		parent::prepare_params();
	}
	
}


//----------------------------------------------------------------------- ?>