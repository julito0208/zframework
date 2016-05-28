<?php

include_once(dirname(__FILE__).'/./../thirdparty/mercadopago/lib/mercadopago.php');

class MercadoPagoHelper
{

	const STATUS_SUCCESS = 'success';
	const STATUS_PENDING = 'pending';
	const STATUS_FAILURE = 'failure';

	public static function generate_url($id, $unit_price, $title='Pago', $callback_url=null, $notification_url=null, $quantity=1, $currency_id=null, $category_id=null)
	{
		$mp = self::create_instance();
		$mp_items = array();

		$mp_items[] = [
			'id' => "{$id}",
			'title' => $title,
			'category_id' => $category_id,
			'quantity' => $quantity,
			"currency_id" => $currency_id ? $currency_id : ZPHP::get_config('mercadopago.currency_id'),
			'unit_price' => ZPHP::is_development_mode() ? 1 : $unit_price,
		];

		if(!$callback_url)
		{
			$callback_url = ZPHP::get_absolute_actual_uri();
		}

		if(!$notification_url)
		{
			$notification_url = URLPattern::reverse(MercadoPagoIPN::get_url_pattern()->get_id());
		}

		$status_varname = ZPHP::get_config('mercadopago.callback_status_varname');

		$preference_data = array(
			"items" => $mp_items,
			"id" => $id,
			"notification_url" => $notification_url,
			'back_urls' => [
				'success' => NavigationHelper::make_url_query(array($status_varname => self::STATUS_SUCCESS), $callback_url),
				'pending' => NavigationHelper::make_url_query(array($status_varname => self::STATUS_PENDING), $callback_url),
				'failure' => NavigationHelper::make_url_query(array($status_varname => self::STATUS_FAILURE), $callback_url),
			]
		);

		$preference = $mp->create_preference($preference_data);


		if(false && ZPHP::is_development_mode())
		{
			$url = $preference['response']['sandbox_init_point'];
		}
		else
		{
			$url = $preference['response']['init_point'];
		}

		return $url;

	}

	public static function generate_category_url($category_id, $id, $unit_price, $title='Pago', $callback_url=null, $notification_url=null, $quantity=1, $currency_id=null)
	{
		return self::generate_url($id, $unit_price, $title, $callback_url, $notification_url, $quantity, $currency_id, $category_id);
	}

	public static function is_callback($status=null)
	{
		$varname = ZPHP::get_config('mercadopago.callback_status_varname');

		if(!$status)
		{
			return isset($_GET[$varname]);
		}
		else
		{
			return isset($_GET[$varname]) && $_GET[$varname] == $status;
		}
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