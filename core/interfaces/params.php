<?php //----------------------------------------------------------------------


interface Params extends GetParam {
	
	public function set_param($name, $value=null);
	
	public function has_param($name);
	
	public function remove_param($name);

}


//--------------------------------------------------------------------------- ?>