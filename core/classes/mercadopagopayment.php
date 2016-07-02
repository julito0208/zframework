<?php


class MercadoPagoPayment
{

	protected static function _format_expire_date($date)
	{
		if(is_object($date) && ClassHelper::is_instance_of($date, 'Date'))
		{
			$format = $date->format_iso();
			$format = preg_replace('#(.+?\-.+?\-.+?\:.+?\:.+?)\-(.+?\:.+?)#', '$1.000-$2', $format);
			return $format;
		}
		else
		{
			return $date;
		}
	}

	/*-------------------------------------------------------------*/


	/**
	*
	* @return $this
	*
	*/

	public static function get_from_preference_id($preference_id=null)
	{
		if(!$preference_id)
		{
			$varname = ZPHP::get_config('mercadopago.preference_id_varname');
			$preference_id = $_GET[$varname];
		}

		$mp = MercadoPagoHelper::create_instance();
		$preference_data = $mp->get_preference($preference_id);

		$payment = new MercadoPagoPayment();


		if (ZPHP::is_development_mode() && ZPHP::get_config('mercadopago.enable_sandbox'))
		{
			$url = $preference_data['response']['sandbox_init_point'];
		}
		else
		{
			$url = $preference_data['response']['init_point'];
		}

		$payment->_url = $url;
		$payment->_preference_id = $preference_id;
		$payment->_additional_info = $preference_data['response']['additional_info'];
		$payment->_external_reference = $preference_data['response']['external_reference'];
		$payment->_notification_url = $preference_data['response']['notification_url'];
		$payment->_payer_phone = $preference_data['response']['payer']['phone']['number'];
		$payment->_payer_address_zip_code = $preference_data['response']['payer']['address']['zip_code'];
		$payment->_payer_address_street_name = $preference_data['response']['payer']['address']['street_name'];
		$payment->_payer_address_street_number = $preference_data['response']['payer']['address']['street_number'];
		$payment->_payer_email = $preference_data['response']['payer']['email'];
		$payment->_payer_identification = $preference_data['response']['payer']['identification']['number'];
		$payment->_payer_name = $preference_data['response']['payer']['name'];
		$payment->_payer_surname = $preference_data['response']['payer']['surname'];
		$payment->_expires_from = $preference_data['response']['expiration_date_from'];
		$payment->_expires_to = $preference_data['response']['expiration_date_to'];
		$payment->_shipment_mode = $preference_data['response']['shipments'][0]['mode'];
		$payment->_shipment_local_pickup = $preference_data['response']['shipments'][0]['local_pickup'];
		$payment->_shipment_dimensions = $preference_data['response']['shipments'][0]['dimensions'];
		$payment->_shipment_receiver_address_zip_code = $preference_data['response']['shipments'][0]['receiver_address']['zip_code'];
		$payment->_shipment_receiver_address_street_name = $preference_data['response']['shipments'][0]['receiver_address']['street_name'];
		$payment->_shipment_receiver_address_street_number = $preference_data['response']['shipments'][0]['receiver_address']['street_number'];
		$payment->_shipment_receiver_address_floor = $preference_data['response']['shipments'][0]['receiver_address']['floor'];
		$payment->_shipment_receiver_address_apartment = $preference_data['response']['shipments'][0]['receiver_address']['apartment'];

		foreach((array) $preference_data['response']['items'] as $item)
		{
			$payment->add_item($item['id'], $item['unit_price'], $item['quantity'], $item['title'], $item['category_id'], $item['currency_id']);

			if(!$payment->get_title())
			{
				$payment->set_title($item['title']);
			}
		}

		return $payment;

	}

	/**
	*
	* @return $this
	*
	*/

	public static function get_from_actual_preference_id()
	{
		return self::get_from_preference_id(null);
	}


	/**
	*
	* @return $this
	*
	*/
	public static function get_from_mercadopago_payment_order(MercadoPagoPaymentOrder $obj)
	{
		return $obj->get_mercadopago_payment();
	}




	/**
	*
	* @return $this
	*
	*/
	public static function get_from_merchant_order($merchant_order)
	{
		
		$payment = new MercadoPagoPayment();

		$payment->_preference_id = $merchant_order['response']['preference_id'];
		$payment->_additional_info = $merchant_order['response']['additional_info'];
		$payment->_external_reference = $merchant_order['response']['external_reference'];
		$payment->_notification_url = $merchant_order['response']['notification_url'];
		$payment->_payer_phone = $merchant_order['response']['payer']['phone']['number'];
		$payment->_payer_address_zip_code = $merchant_order['response']['payer']['address']['zip_code'];
		$payment->_payer_address_street_name = $merchant_order['response']['payer']['address']['street_name'];
		$payment->_payer_address_street_number = $merchant_order['response']['payer']['address']['street_number'];
		$payment->_payer_email = $merchant_order['response']['payer']['email'];
		$payment->_payer_identification = $merchant_order['response']['payer']['identification']['number'];
		$payment->_payer_name = $merchant_order['response']['payer']['name'];
		$payment->_payer_surname = $merchant_order['response']['payer']['surname'];
		//$payment->_expires_from = $preference_data['response']['expiration_date_from'];
		//$payment->_expires_to = $preference_data['response']['expiration_date_to'];

		if(is_array($merchant_order['response']['shipments']) && !empty($merchant_order['response']['shipments']))
		{
			$payment->set_shipment_enabled(true);

			$payment->_shipment_mode = MercadoPagoHelper::SHIPMENT_MERCADO_ENVIOS;
			$payment->_shipment_local_pickup = false;
			// $payment->_shipment_dimensions = $preference_data['response']['shipments'][0]['dimensions'];
			$payment->_shipment_receiver_address_zip_code = $merchant_order['response']['shipments'][0]['receiver_address']['zip_code'];
			$payment->_shipment_receiver_address_street_name = $merchant_order['response']['shipments'][0]['receiver_address']['street_name'];
			$payment->_shipment_receiver_address_street_number = $merchant_order['response']['shipments'][0]['receiver_address']['street_number'];
			$payment->_shipment_receiver_address_floor = $merchant_order['response']['shipments'][0]['receiver_address']['floor'];
			$payment->_shipment_receiver_address_apartment = $merchant_order['response']['shipments'][0]['receiver_address']['apartment'];	
		}
		else
		{
			$payment->set_shipment_enabled(false);			
		}

		foreach((array) $merchant_order['response']['items'] as $item)
		{
			$payment->add_item($item['id'], $item['unit_price'], $item['quantity'], $item['title'], $item['category_id'], $item['currency_id']);

			if(!$payment->get_title())
			{
				$payment->set_title($item['title']);
			}
		}

		return $payment;

	}

	/*-------------------------------------------------------------*/

	protected $_preference_id;
	protected $_additional_info;
	protected $_external_reference;
	protected $_callback_url;
	protected $_notification_url;
	protected $_title;
	protected $_payer_phone;
	protected $_payer_address_zip_code;
	protected $_payer_address_street_name;
	protected $_payer_address_street_number;
	protected $_payer_email;
	protected $_payer_identification;
	protected $_payer_name;
	protected $_payer_surname;
	protected $_expires_from;
	protected $_expires_to;
	protected $_shipment_mode = MercadoPagoHelper::SHIPMENT_MERCADO_ENVIOS;
	protected $_shipment_local_pickup = true;
	protected $_shipment_dimensions = null;
	protected $_shipment_receiver_address_zip_code = null;
	protected $_shipment_receiver_address_street_name = null;
	protected $_shipment_receiver_address_street_number = null;
	protected $_shipment_receiver_address_floor = null;
	protected $_shipment_receiver_address_apartment = null;
	protected $_items = array();
	protected $_items_dimensions = array(0,0,0,0);
	protected $_url;

	/*-------------------------------------------------------------*/

	public function __construct($title=null)
	{
		if(RedirectControl::is_ajax_call())
		{
			$callback_url = NavigationHelper::conv_abs_url(NavigationHelper::navigation_history_get_back_url());
		}
		else
		{
			$callback_url = ZPHP::get_absolute_actual_uri();
		}

		$this->set_callback_url($callback_url);
		$this->set_notification_url(URLPattern::reverse(MercadoPagoIPN::get_url_pattern()->get_id()));

		if($title && ClassHelper::is_instance_of($title, 'MercadoPagoPaymentInterface'))
		{
			$this->set_payment($title);
		}
		else
		{
			$this->set_title($title ? $title : ZPHP::get_config('mercadopago.default_title'));
		}

	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function set_additional_info($value)
	{
		$this->_additional_info = $value;
		return $this;
	}
	
	public function get_additional_info()
	{
		return $this->_additional_info;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function additional_info($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_additional_info($value);
	    }
	    else
	    {
	        return $this->get_additional_info();
	    }   
		
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_callback_url($value)
	{
		$this->_callback_url = $value;
		return $this;
	}

	public function get_callback_url()
	{
		return $this->_callback_url;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function callback_url($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_callback_url($value);
	    }
	    else
	    {
	        return $this->get_callback_url();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_notification_url($value)
	{
		$this->_notification_url = $value;
		return $this;
	}

	public function get_notification_url()
	{
		return $this->_notification_url;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function notification_url($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_notification_url($value);
	    }
	    else
	    {
	        return $this->get_notification_url();
	    }

	}


	public function get_preference_id()
	{
		return $this->_preference_id;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_title($value)
	{
		$this->_title = $value;
		return $this;
	}

	public function get_title()
	{
		return $this->_title;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function title($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_title($value);
	    }
	    else
	    {
	        return $this->get_title();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_phone($value)
	{
		$this->_payer_phone = $value;
		return $this;
	}

	public function get_payer_phone()
	{
		return $this->_payer_phone;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_phone($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_phone($value);
	    }
	    else
	    {
	        return $this->get_payer_phone();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_name($value)
	{
		$this->_payer_name = $value;
		return $this;
	}

	public function get_payer_name()
	{
		return $this->_payer_name;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_name($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_name($value);
	    }
	    else
	    {
	        return $this->get_payer_name();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_surname($value)
	{
		$this->_payer_surname = $value;
		return $this;
	}

	public function get_payer_surname()
	{
		return $this->_payer_surname;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_surname($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_surname($value);
	    }
	    else
	    {
	        return $this->get_payer_surname();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_identification($value)
	{
		$this->_payer_identification = $value;
		return $this;
	}

	public function get_payer_identification()
	{
		return $this->_payer_identification;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_identification($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_identification($value);
	    }
	    else
	    {
	        return $this->get_payer_identification();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_address_zip_code($value)
	{
		$this->_payer_address_zip_code = $value;
		return $this;
	}

	public function get_payer_address_zip_code()
	{
		return $this->_payer_address_zip_code;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_address_zip_code($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_address_zip_code($value);
	    }
	    else
	    {
	        return $this->get_payer_address_zip_code();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_address_street_name($value)
	{
		$this->_payer_address_street_name = $value;
		return $this;
	}

	public function get_payer_address_street_name()
	{
		return $this->_payer_address_street_name;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_address_street_name($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_address_street_name($value);
	    }
	    else
	    {
	        return $this->get_payer_address_street_name();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_address_street_number($value)
	{
		$this->_payer_address_street_number = $value;
		return $this;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function get_payer_address_street_number()
	{
		return $this->_payer_address_street_number;
	}

	/**
	*
	* @return $this
	*
	*/
	public function payer_address_street_number($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_address_street_number($value);
	    }
	    else
	    {
	        return $this->get_payer_address_street_number();
	    }

	}


	/**
	*
	* @return $this
	*
	*/
	public function set_payer_email($value)
	{
		$this->_payer_email = $value;
		return $this;
	}

	public function get_payer_email()
	{
		return $this->_payer_email;
	}

	/**
	 *
	 * @return $this
	 *
	 */
	public function payer_email($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_payer_email($value);
	    }
	    else
	    {
	        return $this->get_payer_email();
	    }

	}


	/**
	*
	* @return $this
	*
	*/
	public function set_payer($email, $name=null, $surname)
	{
		if(ClassHelper::is_instance_of($email, 'MercadoPagoPayer'))
		{
			$this->set_payer_phone($email->get_mp_phone());
			$this->set_payer_address_zip_code($email->get_mp_address_zip_code());
			$this->set_payer_address_street_name($email->get_mp_address_street_name());
			$this->set_payer_address_street_number($email->get_mp_address_street_number());
			$this->set_payer_email($email->get_mp_email());
			$this->set_payer_identification($email->get_mp_identification());
			$this->set_payer_name($email->get_mp_name());
			$this->set_payer_surname($email->get_mp_surname());
		}
		else
		{
			$this->set_payer_email($email);
			$this->set_payer_name($name);
			$this->set_payer_surname($surname);
		}

		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function payer($email, $name=null, $surname)
	{
		return $this->set_payer($email, $name, $surname);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_payer_address($zip_code, $street_name=null, $phone=null, $street_number=null)
	{
		$this->set_payer_address_zip_code($zip_code);
		$this->set_payer_address_street_name($street_name);
		$this->set_payer_address_street_number($street_number);
		$this->set_payer_phone($phone);
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function payer_address($zip_code, $street_name=null, $phone=null, $street_number=null)
	{
		return $this->set_payer_address($zip_code, $street_name, $phone, $street_number);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_external_reference($value)
	{
		$this->_external_reference = $value;
		return $this;
	}

	public function get_external_reference()
	{
		return $this->_external_reference;
	}

	/**
	*
	* @return $this
	*
	*/
	public function external_reference($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_external_reference($value);
	    }
	    else
	    {
	        return $this->get_external_reference();
	    }

	}


	/**
	*
	* @return $this
	*
	*/
	public function set_expires_from($value)
	{
		$this->_expires_from = $value;
		return $this;
	}

	public function get_expires_from()
	{
		return $this->_expires_from;
	}

	/**
	*
	* @return $this
	*
	*/
	public function expires_from($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_expires_from($value);
	    }
	    else
	    {
	        return $this->get_expires_from();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_expires_to($value)
	{
		$this->_expires_to = $value;
		return $this;
	}

	public function get_expires_to()
	{
		return $this->_expires_to;
	}

	/**
	*
	* @return $this
	*
	*/
	public function expires_to($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_expires_to($value);
	    }
	    else
	    {
	        return $this->get_expires_to();
	    }

	}

//	/**
//	*
//	* @return $this
//	*
//	*/
//	public function set_shipment_mode($value)
//	{
//		$this->_shipment_mode = $value;
//		return $this;
//	}

	public function get_shipment_mode()
	{
		return $this->_shipment_mode;
	}

	/**
	*
	* @return $this
	*
	*/
	public function shipment_mode($value=null)
	{
//	    if(func_num_args())
//	    {
//	        return $this->set_shipment_mode($value);
//	    }
//	    else
//	    {
//	        return $this->get_shipment_mode();
//	    }

		return $this->get_shipment_mode();

	}


	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_local_pickup($value)
	{
		$this->_shipment_local_pickup = (boolean) $value;
		return $this;
	}

	public function get_shipment_local_pickup()
	{
		return $this->_shipment_local_pickup;
	}

	/**
	*
	* @return $this
	*
	*/
	public function shipment_local_pickup($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_local_pickup($value);
	    }
	    else
	    {
	        return $this->get_shipment_local_pickup();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_enabled($value)
	{
		return $this->set_shipment_local_pickup(!$value);
	}

	public function get_shipment_enabled()
	{
		return !$this->_shipment_local_pickup;
	}

	/**
	*
	* @return $this
	*
	*/
	public function shipment_enabled($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_enabled($value);
	    }
	    else
	    {
	        return $this->get_shipment_en();
	    }

	}


	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_dimensions($arg1, $arg2=null)
	{

		if(func_num_args() > 1 && $arg1)
		{
			$args = func_get_args();
			$this->_shipment_dimensions = $args;
		}
		else
		{
			$this->_shipment_dimensions = (array) $arg1;
		}
		
		return $this;
	}

	public function get_shipment_dimensions()
	{
		return $this->_shipment_dimensions;
	}

	/**
	*
	* @return $this
	 * alto x ancho x largo x peso (cms, grs)
	*
	*/
	public function shipment_dimensions($arg1=null, $arg2=null)
	{
	    if(func_num_args())
	    {
			if(func_num_args() > 1)
			{
				$args = func_get_args();
				$this->_shipment_dimensions = $args;
			}
			else
			{
				$this->_shipment_dimensions = (array) $arg1;
			}

			return $this;
	    }
	    else
	    {
	        return $this->get_shipment_dimensions();
	    }

	}

	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_receiver_address_zip_code($value)
	{
		$this->_shipment_receiver_address_zip_code = $value;
		return $this;
	}
	
	public function get_shipment_receiver_address_zip_code()
	{
		return $this->_shipment_receiver_address_zip_code;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function shipment_receiver_address_zip_code($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_receiver_address_zip_code($value);
	    }
	    else
	    {
	        return $this->get_shipment_receiver_address_zip_code();
	    }   
		
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_receiver_address_street_name($value)
	{
		$this->_shipment_receiver_address_street_name = $value;
		return $this;
	}
	
	public function get_shipment_receiver_address_street_name()
	{
		return $this->_shipment_receiver_address_street_name;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function shipment_receiver_address_street_name($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_receiver_address_street_name($value);
	    }
	    else
	    {
	        return $this->get_shipment_receiver_address_street_name();
	    }   
		
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_receiver_address_street_number($value)
	{
		$this->_shipment_receiver_address_street_number = $value;
		return $this;
	}
	
	public function get_shipment_receiver_address_street_number()
	{
		return $this->_shipment_receiver_address_street_number;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function shipment_receiver_address_street_number($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_receiver_address_street_number($value);
	    }
	    else
	    {
	        return $this->get_shipment_receiver_address_street_number();
	    }   
		
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_receiver_address_floor($value)
	{
		$this->_shipment_receiver_address_floor = $value;
		return $this;
	}
	
	public function get_shipment_receiver_address_floor()
	{
		return $this->_shipment_receiver_address_floor;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function shipment_receiver_address_floor($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_receiver_address_floor($value);
	    }
	    else
	    {
	        return $this->get_shipment_receiver_address_floor();
	    }   
		
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_shipment_receiver_address_apartment($value)
	{
		$this->_shipment_receiver_address_apartment = $value;
		return $this;
	}

	public function get_shipment_receiver_address_apartment()
	{
		return $this->_shipment_receiver_address_apartment;
	}

	/**
	*
	* @return $this
	*
	*/
	public function shipment_receiver_address_apartment($value=null)
	{
	    if(func_num_args())
	    {
	        return $this->set_shipment_receiver_address_apartment($value);
	    }
	    else
	    {
	        return $this->get_shipment_receiver_address_apartment();
	    }

	}


	/*-------------------------------------------------------------*/
	

	/**
	*
	* @return $this
	*
	*/
	public function add_item($unit_price, $quantity=1, $title=null, $id=null, $category_id=null, $currency_id=null)
	{
		if(is_array($unit_price))
		{
			foreach($unit_price as $a)
			{
				$this->add_item($a);
			}

			return $this;
		}
		else if (ClassHelper::is_instance_of($unit_price, 'MercadoPagoItem'))
		{
			if (!$currency_id)
			{
				$currency_id = ZPHP::get_config('mercadopago.currency_id');
			}


			$this->_items[] = array(
				'id' => $unit_price->get_mp_item_id(),
				'title' => $unit_price->get_mp_item_title(),
				'category_id' => $unit_price->get_mp_item_category_id(),
				'quantity' => (integer) $unit_price->get_mp_item_quantity(),
				"currency_id" => $currency_id,
				'unit_price' => (int) $unit_price->get_mp_item_unit_price(),
			);

			if (ClassHelper::is_instance_of($unit_price, 'MercadoPagoItemDimension'))
			{
				$this->_items_dimensions[0]+=$unit_price->get_mp_dimension_ancho();
				$this->_items_dimensions[1]+=$unit_price->get_mp_dimension_alto();
				$this->_items_dimensions[2]+=$unit_price->get_mp_dimension_largo();
				$this->_items_dimensions[3]+=$unit_price->get_mp_dimension_peso();
			}

			return $this;
		}
		else
		{
			if (!$currency_id)
			{
				$currency_id = ZPHP::get_config('mercadopago.currency_id');
			}

			$this->_items[] = array(
				'id' => "{$id}",
				'title' => $title,
				'category_id' => $category_id,
				'quantity' => (integer)$quantity,
				"currency_id" => $currency_id,
				'unit_price' => $unit_price,
			);

			return $this;
		}
	}

	/**
	*
	* @return $this
	*
	*/
	public function add_category_item($category_id, $id, $unit_price, $quantity=1, $title=null, $currency_id=null)
	{
		return $this->add_item($unit_price, $quantity, $title, $id, $category_id, $currency_id);
	}

	/*-------------------------------------------------------------*/

	protected function _get_preference_data()
	{
		$preference_data = array();

		if($this->_external_reference)
		{
			$preference_data['external_reference'] = $this->_external_reference;
		}

		if($this->_additional_info)
		{
			$preference_data['additional_info'] = $this->_additional_info;
		}

		if($this->_notification_url)
		{
			$preference_data['notification_url'] = $this->_notification_url;
		}

		if($this->_callback_url)
		{
			$status_varname = ZPHP::get_config('mercadopago.callback_status_varname');

			$preference_data['back_urls'] = array(
				'success' => NavigationHelper::make_url_query(array($status_varname => MercadoPagoHelper::STATUS_SUCCESS), $this->_callback_url),
				'pending' => NavigationHelper::make_url_query(array($status_varname => MercadoPagoHelper::STATUS_PENDING), $this->_callback_url),
				'failure' => NavigationHelper::make_url_query(array($status_varname => MercadoPagoHelper::STATUS_FAILURE), $this->_callback_url),
			);
		}

		$preference_data['items'] = $this->_items;

		foreach($preference_data['items'] as $index => $item)
		{
			if(!$item['title'])
			{
				$item['title'] = $this->_title;
			}

			$preference_data['items'][$index] = $item;
		}

		if(!empty($preference_data['items']))
		{
			$preference_data['items'][0]['title'] = $this->_title;
		}
		
		$preference_data['payer'] = $this->_payer;

		$preference_data['payer'] = array (
			'phone' =>
				array (
					'area_code' => '',
					'number' => $this->_payer_phone,
				),
			'address' =>
				array (
					'zip_code' => $this->_payer_address_zip_code,
					'street_name' => $this->_payer_address_street_name,
					'street_number' => $this->_payer_address_street_number,
				),
			'email' => $this->_payer_email,
			'identification' =>
				array (
					'number' => '',
					'type' => $this->_payer_identification,
				),
			'name' => $this->_payer_name,
			'surname' => $this->_payer_surname,
			'date_created' => '',
		);

		if($this->_expires_from || $this->_expires_to)
		{
			$preference_data['expires'] = true;

			if($this->_expires_from)
			{
				$preference_data['expiration_date_from'] = self::_format_expire_date($this->_expires_from);
			}

			if($this->_expires_to)
			{
				$preference_data['expiration_date_to'] = self::_format_expire_date($this->_expires_to);
			}

		}

//		if(!$this->_shipment_local_pickup && $this->_shipment_mode && $this->_shipment_mode == MercadoPagoHelper::SHIPMENT_MERCADO_ENVIOS)
		if(!$this->_shipment_local_pickup)
		{
			$preference_data['shipments'] = array(

//				'mode' => $this->_shipment_mode,
				'mode' => MercadoPagoHelper::SHIPMENT_MERCADO_ENVIOS,
				//'local_pickup' => $this->_shipment_local_pickup,
				'receiver_address' => array(
					'zip_code' => $this->_shipment_receiver_address_zip_code ? $this->_shipment_receiver_address_zip_code : $this->_payer_address_zip_code,
					'street_name' => $this->_shipment_receiver_address_street_name ? $this->_shipment_receiver_address_street_name : $this->_payer_address_street_name, 'street_number' => $this->_shipment_receiver_address_street_number ? $this->_shipment_receiver_address_street_number : $this->_payer_address_street_number,
					'floor' => $this->_shipment_receiver_address_floor,
					'apartment' => $this->_shipment_receiver_address_apartment,
					),
				);


			if(!$this->_shipment_dimensions)
			{
				$shipments_dimensions = $this->_items_dimensions;
			}
			else
			{
				$shipments_dimensions = $this->_shipment_dimensions;
			}

			foreach($shipments_dimensions as $index => $dimension)
			{
				if(!$dimension)
				{
					$dimension = 1;
				}

				$shipments_dimensions[$index] = $dimension;
			}

			$preference_data['shipments']['dimensions'] =
				$shipments_dimensions[0] . 'x' .
				$shipments_dimensions[1] . 'x' .
				$shipments_dimensions[2] . ',' .
				$shipments_dimensions[3];

		}

		return $preference_data;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_payment(MercadoPagoPaymentInterface $payment)
	{

		$this->set_payer($payment->get_mp_payer());
		$this->add_item($payment->get_mp_items());
		$this->set_title($payment->get_mp_title());
		$this->set_external_reference($payment->get_mp_token());
		return $this;
	}

	/*-------------------------------------------------------------*/

	public function get_preference_data()
	{
		return $this->_get_preference_data();
	}

	public function get_url()
	{
		if(!$this->_url)
		{
			$preference_data = $this->_get_preference_data();

			$mp = MercadoPagoHelper::create_instance();
			$preference = $mp->create_preference($preference_data);

			if (ZPHP::is_development_mode() && ZPHP::get_config('mercadopago.enable_sandbox'))
			{
				$url = $preference['response']['sanbox_init_point'];
			} else
			{
				$url = $preference['response']['init_point'];
			}

			if (preg_match('#.+?pref_id\=(?P<id>.+?)$#', $url, $match))
			{
				$this->_preference_id = $match['id'];
			}

			$this->_url = $url;
		}

		return $this->_url;
	}

}

