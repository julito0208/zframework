<?php

interface MercadoPagoPaymentInterface
{
	/**
	*
	* @return MercadoPagoPayer
	*
	*/
	public function get_mp_payer();

	/**
	*
	* @return MercadoPagoItem[]
	*
	*/
	public function get_mp_items();

	public function get_mp_title();

	public function get_mp_token();
}