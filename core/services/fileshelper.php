<?php 

class FilesHelper {

	const FILESYSTEM_NAMES_UTF8 = true;

	//------------------------------------------------------------------------------------------------------------------------------

	public static function path_join($arg1, $arg2=null) {
		$paths = array();
		
		foreach(func_get_args() as $files)
			foreach((array) $files as $file)
				$paths[] = str_replace('\\', '/', $file);
				
		return preg_replace('#\/+#', '/', implode('/', array_filter($paths)));	
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function file_create_temp($contents = null, $prefix = 'temp') { 
	
		$file = tempnam(sys_get_temp_dir(), $prefix ); 
		
		if($contents != null) FilesHelper::file_write($file, $contents );
		
		return $file; 
	}
	
	
	public static function get_site_files_temp_dir() {
		return realpath(ZPHP::get_config('tmp_dir'));
	}
	
	
	
	public static function file_create_site_temp($contents = null, $prefix = 'temp') { 
	
		$file = tempnam(self::get_site_files_temp_dir(), StringHelper::uniqid($prefix));
		
		if($contents != null) FilesHelper::file_write($file, $contents );
		
		return $file; 
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function path_get_extension($filename) {
		
		$basename = basename($filename);
		if(($pos=strrpos($basename, '.')) !== false) return substr($basename, $pos);
		else return '';
	}
	
	
	
	public static function path_remove_extension($filename) {
		
		return preg_replace('#\.([\w]+)$#', '', (string) $filename);
	}
	
	
	
	public static function path_put_extension($filename, $extension, $force=true) {
		
		$extension = trim(strtolower($extension), '.');
		$path_extension = FilesHelper::path_get_extension($filename);
		
		if(!$path_extension || $force) {
			return FilesHelper::path_remove_extension($filename).'.'.$extension;
		} else {
			return $filename;
		}
	}
	
	
	
	public static function file_get_mimetype($filename, $default='application/octet-stream'){
		return MimeTypeHelper::from_extension(FilesHelper::path_get_extension($filename), $default);
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function file_read_array($path) {
		return (array) file($path, FILE_IGNORE_NEW_LINES);
	}
	
	public static function file_write($path, $contents='') {

		self::path_make_dir(dirname($path), $make_parents);
		
		if(is_object($contents) && $contents instanceof MIMEContent) return $contents->save_to($path);
		else return (boolean) file_put_contents($path, $contents);
	}
	
	
	
	public static function file_append($path, $contents='', $new_line=false) {
		
		self::path_make_dir(dirname($path), $make_parents);
		
		$dest_f = fopen($path, 'ab');
		
		if(is_object($contents) && $contents instanceof MIMEContent) {
			
			$temp_source_filename = FilesHelper::file_create_temp($contents);
			$temp_source_f = fopen($temp_source_filename, 'rb');
			
			while(!feof($temp_source_f)) fputs($dest_f, fgets($temp_source_filename));
			
			
			fclose($temp_source_f);
			@ unlink($temp_source_filename);
		
		} else fputs($dest_f, $contents);
		
		if($new_line) fputs($dest_f, "\n");
		
		fclose($dest_f);
		
		return true;
	}
	
	
	public static function file_append_line($path, $contents='') {
		return FilesHelper::file_append($path, $contents, true);
	}
	
	
	
	public static function file_passthru($file) {
		@ $f = fopen($file, 'rb');
		
		if($f) {
			@ fpassthru($f);
			@ fclose($f);
			return true;
			
		} else return false;
	}
	
	
	public static function file_out($file, $attachment=false, $mimetype=null){
		return @ NavigationHelper::content_file_out($file, $attachment, $mimetype);
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	
	public static function path_copy($source, $destination, $overwrite=true) {
		
		$is_dir = @ is_dir($destination);
		if($is_dir) $destination = FilesHelper::path_join(basename($source), $destination);
		
		$file_exists = file_exists($destination);
		
		if(!$file_exists || ($file_exists && $overwrite)) 
			if(!$file_exists || ($file_exists && @ unlink($destination))) 
				return @ copy($source, $destination);
			
		return false;
	}
	
	
	
	public static function path_move($source, $destination, $overwrite=true) {
		
		if(FilesHelper::path_copy($source, $destination, $overwrite)) {
			@ unlink($source);
			return true;
		}
		
		return false;
	}
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function file_walk_array($file, $read_size, $callback, $callback_args=array() ){
		
		@ $file_pointer = fopen($file, 'rb');
		
		if($file_pointer) {
			$callback_args = array_values($callback_args);	
			array_unshift($callback_args, null);
			
			$read_code = '$readed = @ fgets($file_pointer' . ($read_size ? ', ' . ((integer) $read_size) : '') . ');';
			
			while(!feof($file_pointer)) {
				eval($read_code);
				$callback_args[0] = $readed;
				@ call_user_func_array($callback, $callback_args);
			}
			
			@ fclose($file_pointer);
			return true;
			
		} else return false;
	}
	
	
	public static function file_walk($file, $read_size, $callback, $callback_arg1=null, $callback_arg2=null) {
		$args = func_get_args();
		return call_user_func('file_walk_array', $file, $read_size, $callback, array_slice($args, 3));
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function file_transfer_base64($file, $dest, $chunk_size=76, $line_delim="\r\n") {
		
		@ $file_pointer = fopen($file, 'rb');
		
		if($file_pointer) {
			$chunk = '';
			$total = '';
			
			
			while(!feof($file_pointer)) {
				@ $readed = fread($file_pointer, 3);
				$chunk.= base64_encode($readed);
				
				while(strlen($chunk) >= $chunk_size) {
					@ fputs($dest, substr($chunk, 0, $chunk_size) . $line_delim);
					$chunk = substr($chunk, $chunk_size);
				}
			}
			
			if(strlen($chunk) > 0) @ fputs($dest, $chunk . $line_delim);
			@ fclose($file_pointer);
			
			return true;
			
		} else return false;
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	
	public static function dir_list($path, $full_path=false, $filter_callback=false, $filter_full_path=true) {
		
		if(!is_dir($path)) return array();
		
		$dir_resource = opendir($path);
		
		$contents_files = array();
		$contents_dirs = array();
		
		while(($content = readdir($dir_resource))) {

			if($content != '.' && $content != '..') {
				
				$content_full_path = FilesHelper::path_join($path, $content);
				$contents_array_name = is_dir($content_full_path) ? 'contents_dirs' : 'contents_files';
				
				$value = $full_path ? $content_full_path : $content;
				
				if(self::FILESYSTEM_NAMES_UTF8) $value = utf8_decode($value);
				
				
				if(!$filter_callback || call_user_func($filter_callback, $filter_full_path ? $content_full_path : $content)) 	
					${$contents_array_name}["{$value}"] = strtolower($value);
			}
		}
		
		closedir($dir_resource);
		asort($contents_dirs);
		asort($contents_files);
		
		return array_map(array('StringHelper', 'str'), array_merge(array_keys($contents_dirs), array_keys($contents_files)));
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	
	public static function path_unlink($pathname, $recursive=true) {
		
		if(is_array($pathname)) {
			
			$success = true;
			
			foreach($pathname as $p) $success = FilesHelper::path_unlink($p, $recursive) && $success;
			
			return $success;
		
		} else {
		
			if(($is_dir = is_dir($pathname)) && $recursive) 
				FilesHelper::path_unlink(FilesHelper::dir_list($pathname, true), $recursive);
				
			return @ unlink($pathname);
			
		}
	}
	
	
	
	public static function path_make_dir($pathname, $make_parents=true) {
		
		if(is_dir($pathname)) return true;
		
		$parent_pathname = dirname($pathname);
		$parent_is_dir = is_dir($parent_pathname);
		
		if($parent_is_dir || (!$parent_is_dir && $make_parents)) {
			
			if(!$parent_is_dir && !FilesHelper::path_make_dir($parent_pathname, $make_parents)) return false;
			return @ mkdir($pathname);
		
		} else return false;
		
		
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	
	public static function fmove($fp, $offset = 0) {
	
		return @ fseek($fp, ftell($fp) + $offset);
	}
	
	
	public static function fgets_until($fp, $delimiter, $include_delimiter=true, $escape_char=false) {
		
		$readed = '';
		
		$found_delimiter = false;
		
		$delimiter_char_pos = 0;
		$delimiter_length = strlen($delimiter);
		
		$delimiter_readed = '';
		
		if(!$delimiter_length) return $readed;
		
		$escaped = false;
		$escape_char = $escape_char === true ? '\\' : $escape_char;
		
		
		while((@ $char = fgetc($fp)) !== false) {
			
			if($escaped) {
				
				$readed.= $char;
				$escaped = false;
				
			
			} else {
			
				 if($char === $delimiter{$delimiter_char_pos}) {
					
					$delimiter_readed.= $char;
					$delimiter_char_pos++;		
					
					if(strlen($delimiter_readed) == $delimiter_length) {
						
						$found_delimiter = true;
						break;
					}
				
				} else {
					
					if($delimiter_readed) {
						
						$readed.= $delimiter_readed;
						
						$delimiter_char_pos = 0;
						$delimiter_readed = '';
						
					}
					
					$readed.= $char;			
					
					if($escape_char && $char === $escape_char) $escaped = true;
				}
			}
			
		}
		
		if($include_delimiter && $found_delimiter) return $readed.$delimiter;
		else if(!$include_delimiter) return $readed;
		
	}
	
	
	
	public static function abs_path_to_relative($abs_path, $cwd=null) {
		
		if(is_null($cwd)) $cwd = getcwd();
		
		$parts = explode('/', $cwd);
		$count_parent_dir = 0;
		$last_dir = array();
		
		while(count($parts) > 0 && strpos($abs_path, implode('/', $parts)) === false) {
			$count_parent_dir++;
			$last_dir[] = array_pop($parts);
		}
		
		$dir = implode('/', $parts);
		$new_dir = './'.str_repeat('../', $count_parent_dir).substr($abs_path, strlen($dir)+ 1);
		
		return $new_dir;
		
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function get_base64_data($path) {
		
		@ $contents = file_get_contents($path);
		
		return base64_encode($contents);
		
	}

	//------------------------------------------------------------------------------------------------------------------------------

	public static function get_paths_from_expression($arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$paths = array();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				$paths = array_merge($paths, call_user_func_array(array('self', 'get_paths_from_expression'), $arg));
			}
			else
			{
				if(strpos($arg, '*') !== false)
				{
					list($first_part, $second_part)  = explode('*', $arg, 2);

					if(strrpos($first_part, '/') === strlen($first_part) - 1)
					{
						$folder = rtrim($first_part, '/');
						$basename_search = '';

					}
					else
					{
						$folder = dirname($first_part);
						$basename_search = basename($first_part);
					}

					$contents = self::dir_list($folder, true, function($path) use ($basename_search) {

						if($basename_search == '' || stripos(basename($path), $basename_search) === 0)
						{
							return true;
						}

						return false;
					});

					foreach($contents as $path)
					{
						$paths = array_merge($paths, self::get_paths_from_expression($path.$second_part));
					}
				}
				else
				{
					$paths[] = $arg;
				}
			}
		}

		return $paths;
	}
	
	
}
