<?php

interface MercadoPagoItemDimension extends MercadoPagoItem
{
	public function get_mp_dimension_alto();

	public function get_mp_dimension_ancho();

	public function get_mp_dimension_largo();

	public function get_mp_dimension_peso();

}