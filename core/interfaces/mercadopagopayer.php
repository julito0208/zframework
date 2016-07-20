<?php

interface MercadoPagoPayer
{
	public function get_mp_phone();

	public function get_mp_address_zip_code();

	public function get_mp_address_street_name();

	public function get_mp_address_street_number();

	public function get_mp_email();

	public function get_mp_identification();

	public function get_mp_name();

	public function get_mp_surname();
}