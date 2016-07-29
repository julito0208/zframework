<?php

include_once(dirname(__FILE__).'/./../thirdparty/html2pdf/html2pdf.class.php');

class PDF implements MIMEControl
{
	protected static $_DEFAULT_FILENAME = 'document.pdf';

	/*-------------------------------------------------------------*/

	/**
	*
	* @return $this
	*
	*/

	public static function from_html($html, $orientation = 'P', $marges = array(5, 5, 5, 8), $format = 'A4', $encoding='UTF-8', $unicode=true)
	{
		if(is_object($html) && $html instanceof HTMLControl)
		{
			$html = $html->to_string();
		}

		$pdf = new PDF();
		$pdf->_html2pdf = new HTML2PDF($orientation, $format, 'es', $unicode, $encoding, $marges);
		$pdf->_html2pdf->writeHTML($html);

		return $pdf;
	}

	/*-------------------------------------------------------------*/

	/**
	*
	* @var HTML2PDF
	*
	*/
	protected $_html2pdf;

	protected $_filename;

	protected function __construct()
	{
		$this->set_filename(self::$_DEFAULT_FILENAME);
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
		if($this->_html2pdf)
		{
			$this->_html2pdf->Output($this->_filename, 'I');
		}
	}

	public function out_attachment($attachment_filename = null)
	{
		if($this->_html2pdf)
		{
			$this->_html2pdf->Output($attachment_filename ? StringHelper::put_sufix($attachment_filename, '.pdf', true) : $this->_filename, 'FD');
		}

	}

	public function save_to($filename)
	{
		if($this->_html2pdf)
		{
			$this->_html2pdf->Output(StringHelper::put_sufix($filename, '.pdf', true), 'S');
		}

	}

}