<?php

class PDFHTML extends HTMLPageBlank
{

	protected static $_DEFAULT_FILENAME = 'document.pdf';

	/*-------------------------------------------------------------*/

	protected $_filename;

	public function __construct()
	{
		parent::__construct();
		$this->set_filename(self::$_DEFAULT_FILENAME);
	}

	/**
	*
	* @return PDF
	*
	*/
	protected function _get_pdf()
	{
		$html = $this->to_string();
		$html = preg_replace('#(?s)(?m)\<head\>.*?\<\/head\>#', '', $html);

		$pdf = PDF::from_html($html);

		return $pdf;
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/
	public function set_filename($value)
	{
		$this->_filename = StringHelper::put_sufix($value, '.pdf', true);
		return $this;
	}

	public function get_filename()
	{
		return $this->_filename;
	}

	/*-------------------------------------------------------------*/

	public function out()
	{
		$pdf = $this->_get_pdf();
		$pdf->out();
	}

	public function out_attachment($attachment_filename = null)
	{
		$pdf = $this->_get_pdf();
		$pdf->out_attachment($attachment_filename);
	}

	public function save_to($filename)
	{
		$pdf = $this->_get_pdf();
		$pdf->save_to($filename);
	}

}