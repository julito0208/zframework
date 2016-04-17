<?php //----------------------------------------------------------------------


interface MIMEControl {
	
	public function out();
	
	public function out_attachment($attachment_filename=null);
	
	public function save_to($filename);
	
}

