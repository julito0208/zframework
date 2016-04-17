<?php 

@ include_once(ZPHP::get_third_party_path('dunzip2/dZip.inc.php'));
@ include_once(ZPHP::get_third_party_path('dunzip2/dUnzip2.inc.php'));


class ZIPFile implements MIMEControl {

	protected static $_mimetype = 'application/zip';
	
	/*------------------------------------------------------------------------------*/
	
	/**
	 * 
	 * @var dUnzip2
	 */
	protected $_dunzip = null;
	
	/**
	 * 
	 * @var dZip
	 */
	protected $_dzip = null;
	
	protected $_filename = null;
	
	protected $_contents = array();
	
	protected $_dirs = array();
	
	protected $_closed = false;
	
	/*------------------------------------------------------------------------------*/
	
	public function __construct($filename=null) {
				
		if($filename) {
			$this->_dunzip = new dUnzip2($filename);
			$this->_filename = $filename;
			
			
			foreach($this->_dunzip->getList() as $file) 
				$this->_contents[] = $file['file_name'];
			
		
		} else {
			
			$this->_filename = FilesHelper::file_create_temp();
			$this->_dzip = new dZip($this->_filename);
			$this->_dirs = array('/');
		}
	}
	
	
	public function __destruct() {
		$this->close();
	}
		
	/*------------------------------------------------------------------------------*/
	
	public function get_mimetype() {
		return self::$_mimetype;
	}
	
	public function get_list($dir=null, $relative_path=false) {
		
		if($this->_dunzip) {
			
			if($dir && $dir != '/') {
				
				$dir = StringHelper::remove_prefix(StringHelper::put_sufix($dir, '/'), '/');
				$filtered_list = array();
				
				foreach($this->_contents as $filename)
					if(StringHelper::starts_with($filename, $dir) && $dir != $filename)
						$filtered_list[] = !$relative_path ? $filename : StringHelper::remove_prefix($filename, $dir, true);
				
				return $filtered_list;
			
			} else return $this->_contents;
			
		} else return array();
		
	}
	
	
	
	public function get_list_files($dir=null, $relative_path=false) {
		
		$list = $this->get_list($dir, $relative_path);
		$files_list = array();
		
		foreach($list as $filename) 
			if(!StringHelper::ends_with($filename, '/')) $files_list[] = $filename;
		
		return $files_list;	
	}
	
	
	
	public function get_list_dirs($dir=null, $relative_path=false) {
		
		$list = $this->get_list($dir, $relative_path);
		$dirs_list = array();
		
		foreach($list as $filename) 
			if(StringHelper::ends_with($filename, '/')) $dirs_list[] = $filename;
		
		return $dirs_list;	
	}
	
	
	
	/**
	 * 
	 * @return ZIPFile
	 */
	public function extract_dir($dir='/', $target_dir=false) {
		
		if($this->_dunzip) {
						
			$dir = StringHelper::remove_sufix(StringHelper::remove_prefix($dir, '/'), '/');
			
			$target_dir = StringHelper::remove_sufix($target_dir ? $target_dir : '', '/');
			$source_dirs = $this->get_list_dirs($dir);
			
			foreach($source_dirs as $source_dir) {
				
				$dest_dir = FilesHelper::path_join($target_dir, StringHelper::remove_prefix($source_dir, $dir));				
				if(!file_exists($dest_dir) && !is_dir($dest_dir)) @ mkdir($dest_dir);
			}
			
					
			$source_files = $this->get_list_files($dir);
						
			foreach($source_files as $filename) {
				
				$dest_filename = FilesHelper::path_join($target_dir, StringHelper::remove_prefix($filename, $dir));
				@ $this->_dunzip->unzip($filename, $dest_filename);
			}
				
		}
		
		return $this;
	}
	
	
	
	
	
	/**
	 * 
	 * @return ZIPFile
	 */
	public function extract_file($filename, $target=false) {
		
		if($this->_dunzip) {
						
			$target = StringHelper::remove_sufix($target ? $target : getcwd(), '/');
			
			if(is_dir($target)) $target = FilesHelper::path_join($target, basename($filename));
			
			FilesHelper::path_make_dir(dirname($target));
			
			$this->_dunzip->unzip($filename, $target);
			
		}
		
		return $this;
	}
	
	
	
	
	public function extract_temp($filename) {
		
		if($this->_dunzip) {
			
			$temp_filename = FilesHelper::file_create_temp();
			$this->extract_file($filename, $temp_filename);
			return $temp_filename;
			
		} else return null;
	}
	
	
	
	public function extract_data($filename) {
		
		if($this->_dunzip) {
			
			$temp_filename = $this->extract_temp($filename);
			return file_get_contents($temp_filename);
			
		} else return null;
	}
	
	
	
	//--------------------------------------------------------------------------------------
	
	
	/**
	 * 
	 * @return ZIPFile
	 */
	public function create_dir($dirname, $make_parents=true) {
		
		if($this->_dzip) {
			
			$dirname = StringHelper::put_sufix(StringHelper::put_prefix($dirname, '/'), '/');
			
			if(!in_array($dirname, $this->_dirs) && $dirname != '/./') {
				
				$parent_dirname = dirname($dirname);
				
				if($make_parents || in_array($parent_dirname, $this->_dirs)) {
					
					if(!in_array($parent_dirname, $this->_dirs)) $this->create_dir($parent_dirname, $make_parents);
			
					$this->_dirs[] = $dirname;
					$this->_dzip->addDir($dirname);
				}
				
			}
			
		}
		
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return ZIPFile
	 */
	public function add_file($pathname, $target=null) {

		if($this->_dzip) {
									
//			if(is_object($pathname) && $pathname instanceof MIMEFile) {
//				$pathname = FilesHelper::file_create_temp($pathname);
//				if(!$target) $target = $pathname->get_file();
//				
//			}
			
			if(!$target) $target = '/'.basename($pathname);
			else if(in_array(($dir_target = StringHelper::put_sufix(StringHelper::put_prefix($target, '/'), '/')), $this->_dirs)) $target = $dir_target . basename($pathname);
						
			$dirname = dirname($target);
			
			$this->create_dir($dirname);			
						
			if(is_file($pathname)) {
						
				$this->_dzip->addFile($pathname, $target);
			
			} else if(is_dir($pathname)) {
				
				if($target != '.') $this->create_dir($target);
				
				foreach(FilesHelper::dir_list($pathname, true) as $dir_content)  					
					$this->add_file($dir_content, $target);
				
					
			}
			
		}
		
		return $this;
		
	}
	
	
	
	/**
	 * 
	 * @return ZIPFile
	 */
	public function add_file_data($data, $target) {
		
		if($this->_dzip) return $this->add_file(FilesHelper::file_create_temp($data), $target);			

		else return $this;
		
	}
	
	
	/*------------------------------------------------------------------------------*/
	
	
	public function close() {
		
		if(!$this->_closed) {
			
			if($this->_dunzip) $this->_dunzip->close();
						
			$this->_closed = true;
		}
	}
	

	//--------------------------------------------------------------------------------------
	
	public function out() {

		if($this->_dunzip) {
			
			FilesHelper::file_out($this->_filename, false, $this->get_mimetype());
		
		} else {
			
			$this->_dzip->save();
			FilesHelper::file_out($this->_filename, false, $this->get_mimetype());
			
		}
	}
	
	
	public function out_attachment($filename=null) {
		
		if($this->_dunzip) {
			
			FilesHelper::file_out($this->_filename, basename($filename ? $filename : $this->_filename), $this->get_mimetype());
		
		} else {
			
			$this->_dzip->save();
			FilesHelper::file_out($this->_filename, basename($filename ? $filename : $this->_filename), $this->get_mimetype());
		}
	}
		
		
	/**
	 * 
	 * @return ZIPFile
	 */
	public function save_to($filename) {
		
		if($this->_dunzip) {
			
			copy($this->_filename,$filename);
			return new ZIPFile($filename);
			
		} else {
			
			$this->_dzip->save();
			copy($this->_filename, $filename);
			
			return new ZIPFile($filename);
		}
		
		
	}
	

}
