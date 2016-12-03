<?php

class JSONOptionItemHTML extends JSONOptionItem implements OptionItemHTML
{


	/**
	 *
	 * @return JSONOptionItemHTML
	 *
	 */
	public static function parse_option_item($option_label, $value=null, $html=null)
	{
		$option_item = new JSONOptionItemHTML($option_label, $value, $html);
		return $option_item;
	}

	/**
	 *
	 * @return JSONOptionItemHTML[]
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
	public static function parse_option_array($option_label, $value=null, $html=null)
	{
		$option_item = self::parse_option_item($option_label, $value, $html);
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

	protected static $_html_fields_varnames = array('html');

	/**
	 *
	 * @return array
	 *
	 */
	protected static function _parse_array_option($option_label, $value=null, $html=null) {

		if(!is_null($value)) {

			$option = array(self::TEXT_NAME => $option_label, self::VALUE_NAME => $value);
			$option[self::HTML_NAME] = !is_null($html) ? $html : $option_label;
			return $option;

		} else {

			if($option_label && $option_label instanceof OptionItem) {

				$option = array(self::TEXT_NAME => $option_label->get_option_item_label(), self::VALUE_NAME => $option_label->get_option_item_value());
				$option[self::HTML_NAME] = ($option_label instanceof OptionItemHTML) ? $option_label->get_option_item_html() : $option[self::TEXT_NAME];
				return $option;

			} else {

				if($option_label instanceof DBEntity) {

					$primary_keys = $option_label->primary_keys;
					$fields = $option_label->fields;

					if(count($primary_keys) == 1) {

						$option_value = $option_label->$primary_keys[0];
						$option_text = '';
						$option_html = null;

						foreach(self::$_text_fields_varnames as $key) {

							if(in_array($key, $fields))
							{
								$option_text = $option_label->$key;
								break;
							}
						}

						foreach(self::$_html_fields_varnames as $key) {

							if(in_array($key, $fields))
							{
								$option_html = $option_label->$key;
								break;
							}
						}

						return array(self::TEXT_NAME => $option_text, self::VALUE_NAME => $option_value, self::HTML_NAME => is_null($option_html) ? $option_text : $option_html);
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

				return array(self::TEXT_NAME => $option_text, self::VALUE_NAME => $option_value, self::HTML_NAME => $option_text);
			}

		}

		return array(self::TEXT_NAME => null, self::VALUE_NAME => null, self::HTML_NAME => null);
	}

	/*-------------------------------------------------------------*/

	public function __construct($option_label=null, $value=null, $html=null)
	{
		parent::__construct();

		if($option_label)
		{
			$this->set_option($option_label, $value, $html);
		}
	}



	public function __toArray()
	{
		return array(
			self::TEXT_NAME => $this->get_text(),
			self::VALUE_NAME => $this->get_value(),
			self::HTML_NAME => $this->get_html(),
		);
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function set_option($option_label, $value=null, $html=null)
	{
		$option = self::_parse_array_option($option_label, $value, $html);
		$this->set_value($option[self::VALUE_NAME]);
		$this->set_text($option[self::TEXT_NAME]);
		$this->set_html($option[self::HTML_NAME]);
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_html($html)
	{
		$this->_json_map->set_item(self::HTML_NAME, $html);
		return $this;
	}

	public function get_html()
	{
		return $this->_json_map->get_item(self::HTML_NAME);
	}

	public function get_option_item_html()
	{
		return $this->get_html();
	}

}