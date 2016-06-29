<?php

include_once(dirname(__FILE__).'/./../thirdparty/mercadopago/lib/mercadopago.php');

class MercadoPagoHelper
{

	const STATUS_SUCCESS = 'success';
	const STATUS_PENDING = 'pending';
	const STATUS_FAILURE = 'failure';

	const SHIPMENT_CUSTOM = 'custom';
	const SHIPMENT_MERCADO_ENVIOS = 'me2';
	const SHIPMENT_NOT_SPECIFIED = 'not_specified';

	/*-------------------------------------------------------------*/

	public static function is_callback($status=null)
	{
		$varname_status = ZPHP::get_config('mercadopago.callback_status_varname');
		$varname_preference = ZPHP::get_config('mercadopago.preference_id_varname');

		$is_callback = isset($_GET[$varname_status]) && isset($_GET[$varname_preference]);

		if(!$status || !$is_callback)
		{
			return $is_callback;
		}

		return $is_callback && $_GET[$varname_status] == $status;

	}

	public static function is_callback_success()
	{
		return self::is_callback(self::STATUS_SUCCESS);
	}

	public static function is_callback_pending()
	{
		return self::is_callback(self::STATUS_PENDING);
	}


	public static function is_callback_failure()
	{
		return self::is_callback(self::STATUS_FAILURE);
	}


	/*-------------------------------------------------------------*/

	/**
	 *
	 * @return MP
	 *
	 */
	public static function create_instance()
	{
		$mp_client_id = ZPHP::get_config('mercadopago.clientid');
		$mp_client_secret = ZPHP::get_config('mercadopago.clientsecret');

		$mp = new MP ($mp_client_id, $mp_client_secret);

		return $mp;
	}


}