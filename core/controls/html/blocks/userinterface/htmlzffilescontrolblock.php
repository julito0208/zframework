<?php //-----------------------------------------------------------------------


class HTMLZFFilesControlBlock extends HTMLInputControl {

	const REQUEST_DELETE_VARNAME = 'files-delete';
	
	/*-------------------------------------------------------------*/

	public static function handle_remove_files($entity=null)
	{
		$id_files = explode(',', $_REQUEST[self::REQUEST_DELETE_VARNAME]);

		if($entity)
		{
			call_user_func(array($entity, 'delete_rows'), array('id_file' => $id_files));
		}

		ZfFile::delete_rows(array('id_file' => $id_files));
	}

	/*-------------------------------------------------------------*/

	protected $_files = array();
	protected $_enable_input = true;
	protected $_enable_delete = false;

	public function __construct($id=null, $name=null) {

		parent::__construct();

		$this->set_id($id);
		$this->set_name($name);
	}

	/**
	*
	* @return $this
	*
	*/
	public function add_files($files)
	{
		$this->_files = array_merge($this->_files, (array) $files);
		return $this;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_enable_input($value)
	{
		$this->_enable_input = $value;
		return $this;
	}

	public function get_enable_input()
	{
		return $this->_enable_input;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_enable_delete($value)
	{
		$this->_enable_delete = $value;
		return $this;
	}

	public function get_enable_delete()
	{
		return $this->_enable_delete;
	}



	public function prepare_params() {

		parent::prepare_params();

		$this->set_param('enable_input', $this->_enable_input);
		$this->set_param('enable_delete', $this->_enable_delete); 

		$id_files = array();

		foreach($this->_files as $file)
		{
			$id_files[] = is_object($file) ? $file->id_file : $file;
		}

		$files = ZfFile::list_all(array('id_file' => $id_files), 'title');

		$this->set_param('files', $files);

		$input = new HTMLInputFileControl($this->_id, $this->_name);
		$input->set_style("background: #EEE; border: solid 1px #999; width: 350px; padding: 5px 10px; border-radius: 5px; box-shadow: 1px 1px 1px rgba(0,0,0,0.2);");
		$input->set_multiple(true);

		$this->set_param('input', $input);
	}

}


//----------------------------------------------------------------------- ?>