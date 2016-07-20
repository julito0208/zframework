<?php

class HTMLMercadoPagoCallbackMessageBlock extends HTMLControl
{

	/**
	*
	* @return $this
	*
	*/
	public static function get_message_html()
	{
		$block = new HTMLMercadoPagoCallbackMessageBlock();
		return $block->to_string();
	}

	/*-------------------------------------------------------------*/

	protected $_message;
	protected $_message_type;

	public function __construct()
	{
		parent::__construct();
		self::add_global_static_library(self::STATIC_LIBRARY_NOTIFY);

		if(MercadoPagoHelper::is_callback())
		{
			if(MercadoPagoHelper::is_callback_success())
			{
				$this->_message = 'Su pago ha sido realizado con &eacute;xito';
				$this->_message_type = 'success';
			}
			else if(MercadoPagoHelper::is_callback_pending())
			{
				$this->_message = 'Su pago est&aacute; pendiente';
				$this->_message_type = 'info';
			}
			if(MercadoPagoHelper::is_callback_failure())
			{
				$this->_message = 'No se pudo realizar el pago';
				$this->_message_type = 'danger';
			}

		}
	}

	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('message', $this->_message);
		$this->set_param('message_type', $this->_message_type);
	}
}