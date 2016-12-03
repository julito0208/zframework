<?php

class JSONOptionItem implements OptionItem, MIMEControl
{

	/**
	*
	* @return JSONOptionItem
	*
	*/
	public static function parse_option_item($option_label, $value=null)
	{
		$option_item = new JSONOptionItem($option_label, $value);
		return $option_item;
	}

	/**
	 *
	 * @return JSONOptionItem[]
	 *
	 */
	public static function parse_option_item_list($options=array())
	{
		$options_items = array();

		foreach($options as $option)
		{
			$options_items[] = self::parse_option_item($option);
		}

		return $options_items;
	}

	/**
	*
	* @return array
	*
	*/
	public static function parse_option_array($option_label, $value=null)
	{
		$option_item = self::parse_option_item($option_label, $value);
		return $option_item->__toArray();
	}


	/**
	 *
	 * @return array
	 *
	 */
	public static function parse_option_array_list($options=array())
	{
		$options_items = array();

		foreach($options as $option)
		{
			$options_items[] = self::parse_option_array($option);
		}

		return $options_items;
	}

	/*-------------------------------------------------------------*/

	protected static $_text_fields_varnames = array('text','label','title','nombre','name','titulo', self::TEXT_NAME);

	/**
	*
	* @return array
	*
	*/
	protected static function _parse_array_option($option_label, $value=null) {

		if(!is_null($value)) {

			return array(self::TEXT_NAME => $option_label, self::VALUE_NAME => $value);

		} else {

			if($option_label && $option_label instanceof OptionItem) {

				return array(self::TEXT_NAME => $option_label->get_option_item_label(), self::VALUE_NAME => $option_label->get_option_item_value());

			} else {

				if($option_label instanceof DBEntity) {

					$primary_keys = $option_label->primary_keys;
					$fields = $option_label->fields;

					if(count($primary_keys) == 1) {

						$option_value = $option_label->$primary_keys[0];
						$option_text = '';

						foreach(self::$_text_fields_varnames as $key) {

							if(in_array($key, $fields))
							{
								$option_text = $option_label->$key;
								break;
							}
						}

						return array(self::TEXT_NAME => $option_text, self::VALUE_NAME => $option_value);
					}
				}

				$option = CastHelper::to_array($option_label);

				$option_value = $option[self::VALUE_NAME];
				$option_text = '';

				foreach(self::$_text_fields_varnames as $key) {

					if($option[$key]) {

						$option_text = $option[$key];
						break;
					}

				}

				return array(self::TEXT_NAME => $option_text, self::VALUE_NAME => $option_value);

			}

		}

		return array(self::TEXT_NAME => null, self::VALUE_NAME => null);
	}

	/*-------------------------------------------------------------*/

	protected $_json_map;

	public function __construct($option_label=null, $value=null)
	{
		$this->_json_map = new JSONMap();

		if($option_label)
		{
			$this->set_option($option_label, $value);
		}
	}

	public function __toArray()
	{
		return array(
			self::TEXT_NAME => $this->get_text(),
			self::VALUE_NAME => $this->get_value(),
		);
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function set_option($option_label, $value=null)
	{
		$option = self::_parse_array_option($option_label, $value);
		$this->set_value($option[self::VALUE_NAME]);
		$this->set_text($option[self::TEXT_NAME]);
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_value($value)
	{
		$this->_json_map->set_item(self::VALUE_NAME, $value);
		return $this;
	}

	public function get_value()
	{
		return $this->_json_map->get_item(self::VALUE_NAME);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_text($value)
	{
		$this->_json_map->set_item(self::TEXT_NAME, $value);
		return $this;
	}

	public function get_text()
	{
		return $this->_json_map->get_item(self::TEXT_NAME);
	}

	/*-------------------------------------------------------------*/
	
	public function get_option_item_label()
	{
		return $this->get_text();
	}

	public function get_option_item_value()
	{
		return $this->get_value();
	}

	/*-------------------------------------------------------------*/
	public function out()
	{
		return $this->_json_map->out();
	}

	public function out_attachment($attachment_filename = null)
	{
		return $this->_json_map->out_attachment($attachment_filename);
	}

	public function save_to($filename)
	{
		return $this->_json_map->save_to($filename);
	}


}