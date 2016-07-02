<?php

interface MercadoPagoItem
{
	public function get_mp_item_id();

	public function get_mp_item_title();

	public function get_mp_item_category_id();

	public function get_mp_item_quantity();

	public function get_mp_item_unit_price();

}