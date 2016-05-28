<?php

class MercadoPagoIPN extends HTMLPageBlank
{
	public static function get_url_pattern() {
		$url = ZPHP::get_config('mercadopago.ipn_url');
		return new URLPattern(preg_quote($url), 'MercadoPagoIPN', 'MercadoPagoIPN');
	}

	/*-------------------------------------------------------------*/

	protected static $_callbacks = array();

	public static function add_callback($function)
	{
		self::$_callbacks[] = $function;
	}

	/*-------------------------------------------------------------*/

	public function __construct()
	{
		parent::__construct();

		$ids = array();
		$mp = MercadoPagoHelper::create_instance();

		// Get the payment and the corresponding merchant_order reported by the IPN.
		if($_GET["topic"] == 'payment'){
			$payment_info = $mp->get("/collections/notifications/" . $_GET["id"]);
			$merchant_order_info = $mp->get("/merchant_orders/" . $payment_info["response"]["collection"]["merchant_order_id"]);
			// Get the merchant_order reported by the IPN.
		} else if($_GET["topic"] == 'merchant_order'){
			$merchant_order_info = $mp->get("/merchant_orders/" . $_GET["id"]);
		}

		file_put_contents(ZPHP::get_www_dir().'/test', var_export($payment_info, true), FILE_APPEND);
		file_put_contents(ZPHP::get_www_dir().'/test', var_export($merchant_order_info, true), FILE_APPEND);

		$vars = compact('payment_info', 'merchant_order_info');

		if ($merchant_order_info["status"] == 200) {
			// If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
			$paid_amount = 0;

			foreach ($merchant_order_info["response"]["payments"] as  $payment) {
				if ($payment['status'] == 'approved'){
					$paid_amount += $payment['transaction_amount'];
				}
			}

			if($paid_amount >= $merchant_order_info["response"]["total_amount"] || true){
				if(count($merchant_order_info["response"]["shipments"]) > 0) { // The merchant_order has shipments
					if($merchant_order_info["response"]["shipments"][0]["status"] == "ready_to_ship"){
//						print_r("Totally paid. Print the label and release your item.");
					}
				} else { // The merchant_order don't has any shipments


					foreach($merchant_order_info["response"]['items'] as $item)
					{
//						$quantity = $item['quantity'];
						$id = $item['id'];
//						list($id_carrito_pago_token, $id_producto_stock) = explode('-', $id);
//
//						$producto_stock = CmsCatalogoProductoStock::get_by_id_producto_stock($id_producto_stock);
//						$producto_stock->move_stock(-$quantity);
//						$producto_stock->save();
						$ids[] = $id;
					}

//					if($id_carrito_pago_token)
//					{
//						$carrito = CmsCarrito::get_row(['pago_token' => $id_carrito_pago_token]);
//						$carrito->set_id_estado(CmsCarritoEstado::ESTADO_PAGADO);
//						$carrito->set_descripcion_pago(json_encode($vars));
//						$carrito->save();
//
//						$email = new AdminCompraEmail($carrito);
//						$email->send();
//
//						$email = new UsuarioCompraEmail($carrito);
//						$email->send();
//					}
//					print_r("Totally paid. Release your item.");
				}
			} else {
				//print_r("Not paid yet. Do not release your item.");
			}
		}

		$ids = array_unique($ids);

		foreach(self::$_callbacks as $callback)
		{
			foreach($ids as $id)
			{
				@ call_user_func($callback, $id);
			}
		}
	}

}