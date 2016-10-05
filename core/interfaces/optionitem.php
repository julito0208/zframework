<?php

interface OptionItem {

	const VALUE_NAME = 'value';
	const TEXT_NAME = 'text';
	
	public function get_option_item_label();
	
	public function get_option_item_value();
	
}