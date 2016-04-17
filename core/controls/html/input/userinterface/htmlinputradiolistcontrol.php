<?php //-----------------------------------------------------------------------


class HTMLInputRadioListControl extends HTMLInputControl {
	
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
	
	
	/* @return HTMLInputSelectControl */
	public function add_option($option_label, $value='') {
		
		if(func_num_args() == 2) {
			
			$this->_options[] = array('label' => $option_label, 'value' => $value, 'checked' => false);
			
		} else {
			
			if($option_label && $option_label instanceof OptionItem) {
				
				$this->_options[] = array('label' => $option_label->get_option_item_label(), 'value' => $option_label->get_option_item_value(), 'checked' => false);
				
			} else {
				
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

	
	/* @return HTMLInputSelectControl */
	public function clear_options() {
		$this->_options = array();
		return $this;
	}
	
	
	/* @return HTMLInputSelectControl */
	public function add_options(array $options) {
		
		foreach((array) $options as $option) {
			$this->add_option($option);
		}
		
		return $this;
	}
	
	/* @return HTMLInputSelectControl */
	public function set_options(array $options) {
		
		$this->clear_options();
		$this->add_options($options);
		return $this;
	}
	
	public function set_value($value) {
		
		if($value && $value instanceof OptionItem) {
			$value = $value->get_option_item_value();
		}
		
		return parent::set_value($value);
	
	}
	

	//-----------------------------------------------------------------------
	
	
	public function prepare_params() {
		
		parent::prepare_params();
		
		$inputs = array();
		
		foreach($this->_options as $index => $option) {
			
			$input = new HTMLInputRadioControl($this->get_id().'_'.$option['value'].'_'.$index, $this->get_name().'[]');
			$input->set_title($option['label']);
			$input->set_value($option['value']);
			$input->set_checked($option['value'] == $this->get_value());
			
			$inputs[] = $input;
			
		}
		
		$this->set_param('inputs', $inputs);

	}
	
}


//----------------------------------------------------------------------- ?>