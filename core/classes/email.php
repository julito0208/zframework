<?php

abstract class Email extends MVParamsContentControl {

	const LOG_NAME = 'email';
	const EMAIL_SEPARATOR = ';';

	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected static $_email_pattern = '(?:[a-zA-Z0-9_\.\-\+]+?)\@(?:(?:(?:[a-zA-Z0-9\-])+\.)+(?:[a-zA-Z0-9]{2,4})+)';
	
	public static function validate_email($email, &$match=null) {
		return (boolean) preg_match('/^' . self::$_email_pattern . '$/', $email, $match);
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------

	protected static $_default_from_email = 'email@email.com';
	protected static $_default_from_name = 'Email';
	protected static $_default_charset = 'iso-8859-15';
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected $_subject;
			
	protected $_from_name;
	protected $_from_email;
	
	protected $_reply_email;
	protected $_reply_name;
	
	protected $_smtp_server;
	protected $_smtp_protocol;
	protected $_smtp_port;
	protected $_smtp_ssl;
	protected $_smtp_user_login;
	protected $_smtp_user_pass;

	protected $_to = array();
	protected $_cc = array();
	protected $_cco = array();
	
	protected $_text_plain_charset;
	protected $_text_html_charset;

	protected $_attachments = array();
	protected $_embedded = array();
	
	protected $_error = null;
	
	protected $_smtp_connection_id;
	
	protected $_parsed_html = '';
	protected $_parsed_text = '';
	
	public function __construct($params=null) {

		parent::__construct($params);
		
		$this->set_from_email(ZPHP::get_config('email_from_email', self::$_default_from_email));
		$this->set_from_name(ZPHP::get_config('email_from_name', self::$_default_from_name));
		
		$this->set_reply_email(ZPHP::get_config('email_reply_email', $this->get_from_email()));
		$this->set_reply_name(ZPHP::get_config('email_reply_name', $this->get_from_name()));

		$this->set_smtp_server(ZPHP::get_config('email_smtp_server'));
		$this->set_smtp_ssl(ZPHP::get_config('email_smtp_ssl'));
		$this->set_smtp_port(ZPHP::get_config('email_smtp_port'));
		$this->set_smtp_protocol(ZPHP::get_config('email_smtp_protocol'));
		$this->set_smtp_user_login(ZPHP::get_config('email_smtp_user_login'));
		$this->set_smtp_user_pass(ZPHP::get_config('email_smtp_user_pass'));
		
		$this->set_text_charset(ZPHP::get_config('charset', self::$_default_charset));
		
		$this->_set_parse_all_parents_templates(true);

		$this->set_param('site_url', ZPHP::get_site_url());
	}

	//------------------------------------------------------------------------------------------------------------------------------------
	
	abstract protected function _get_parsed_content();
	
	abstract protected function _set_text_charset($charset);
	
	abstract protected function _get_text_charset();
	
	/* returns null if only html */
	abstract protected function _get_content_text_plain();
	
	/* returns null if only plain text */
	abstract protected function _get_content_text_html();
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	private function _smtp_read($return_msg=false) {
		
		$msg = '';
		$code = 0;
		
		while(true) {
			@ $server_answer = fgets($this->_smtp_connection_id);
		
			if(ZPHP::get_config('email_log_commands'))
			{
				LogFile::log_file(self::LOG_NAME, 'Get: '.$server_answer);
			}

			if((boolean) preg_match('/^(?P<code>\d{3})(?P<continue>\-)?(?P<msg>.*?)\s*$/', $server_answer, $answer_match)) {
				
				$code = (integer) $answer_match['code'];
				$msg.= $answer_match['msg'];
				if(!$answer_match['continue']) break;
				
			} else break;				
		}
		
		if($return_msg) return array($code, $msg);
		else return $code;
	}
	
	
	private function _smtp_send($data, $read=false, $return_msg=false){
		
		if(ZPHP::get_config('email_log_commands'))
		{
			LogFile::log_file(self::LOG_NAME, 'Send: '.$data);
		}
		
		@ fputs($this->_smtp_connection_id, $data);
		
		if($read) return $this->_smtp_read($return_msg);
	}
	
	
	private function _smtp_command($data, $read=true, $return_msg=false){
		return $this->_smtp_send("{$data}\r\n", $read, $return_msg);		
	}
	
	
	private function _smtp_close(){
		if($this->_smtp_connection_id) $this->_smtp_command('QUIT', false);
		@ fclose($this->_smtp_connection_id);
		$this->_smtp_connection_id = null;
		
	}
	
	
	private function _smtp_open(){
		$this->_smtp_close();
		
		if(!$this->_smtp_server) return $this->_set_error('No se especifico el servidor SMTP');
				
		$protocol = !is_null($this->_smtp_protocol) ? $this->_smtp_protocol : ($this->_smtp_ssl ? 'tls' : null);
		$port = (integer) ( $this->_smtp_port ? $this->_smtp_port : ( $this->_smtp_ssl ? 465 : 25 ) );
		
		$host = ($protocol ? "{$protocol}://" : '') . $this->_smtp_server;

		$this->_smtp_connection_id = fsockopen($host, $port);
									
		if(!$this->_smtp_connection_id) return $this->_set_error("No se pudo conectar al host smtp: {$host}:{$port}");
		
		$this->_smtp_read();
		
		if(!in_array($this->_smtp_command("EHLO {$_SERVER['SERVER_NAME']}"),array(220,250))) return $this->_set_error("Error al iniciar comunicacion con el servidor: {$host}:{$port}");
						
		if($this->_smtp_user_login) {
			
			$code = $this->_smtp_command("AUTH LOGIN");
			$code = $this->_smtp_command(base64_encode($this->_smtp_user_login));
			$code = $this->_smtp_command(base64_encode($this->_smtp_user_pass));
			
			if($code==535 || $code!=235) return $this->_set_error("Fallo la autenticacion con el servidor");
		}
		
		return true;
	}
	
	
	protected function _smtp_ping() {
		
		if(!$this->_smtp_connection_id) return $this->_smtp_open();
		else {
			if($this->_smtp_command("NOOP") != 250) return $this->_smtp_open();
			else return true;
			
		}
	}
	

	//------------------------------------------------------------------------------------------------------------------------------------

	
	protected function _set_error($error) {
		$this->_error = $error;
		
		if(ZPHP::get_config('email_log_errors'))
		{
			LogFile::log_error_file(self::LOG_NAME, $error);
		}
		
		return false;
	}
	
	protected function _clear_error() {
		$this->_error = null;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	protected function _send() {
		
		$total_size = 0;
		
		$text_plain = $this->_get_content_text_plain();
		$text_html = $this->_get_content_text_html();
		
		if(!is_null($text_plain)) {
			$old_content = $this->_content;
			$this->_content = $text_plain;
			$text_plain = $this->to_string();
			$this->_content = $old_content;
		}
		
		if(!is_null($text_html)) {
			$old_content = $this->_content;
			$this->_content = $text_html;
			$text_html = $this->to_string();
			$this->_content = $old_content;
		}
		
		$this->_parsed_html = '';
		$this->_parsed_text = '';
		
		//------------------------------------------------------------------------------------------------------------------------------------

		if(!$this->_from_email) return $this->_set_error('No se especifico el email del remitente');
		if(empty($this->_to) && empty($this->_cc) && empty($this->_cco)) return $this->_set_error('No se especificaros destinatarios');
		
		if(!$this->_smtp_ping()) return false;	
		
		if($this->_smtp_command("MAIL FROM: <{$this->_from_email}>") == 535 ) 
			return $this->_set_error('Error desconocido');
		
		foreach(array_merge($this->_to, $this->_cc, $this->_cco) as $rcpt) 
			$this->_smtp_command( "RCPT TO: <{$rcpt['email']}>");
		
					
		foreach($this->_attachments as $attachment) $total_size += $attachment['total_size'];
		foreach($this->_embedded as $content) $total_size += $content['total_size'];
		
		//------------------------------------------------------------------------------------------------------------------------------------
			
		$header = '';
		
		$reply_name = $this->_reply_name ? $this->_reply_name : $this->_from_name; 
		$reply_email = $this->_reply_email ? $this->_reply_email : $this->_from_email; 
		
		$header.= "Reply-To: ".($reply_name ? HTMLHelper::escape($reply_name).' ' : "")."<{$reply_email}>\r\n";

//		$header.= "Return-Path: ".($this->_from_name ? HTMLHelper::escape($this->_from_name).' ' : "")."<{$this->_from_email}>\r\n";
		$header.= "From: ".($this->_from_name ? HTMLHelper::escape($this->_from_name).' ' : "")."<{$this->_from_email}>\r\n";
		$header.= "Organization: \"".ZPHP::get_site_name()."\"\r\n";	
		$header.= "X-TM-AS-User-Approved-Sender: Yes\r\n";	
		$header.= "MIME-version: 1.0\r\n";	
		$header.= "X-Originating-Email: [{$this->_from_email}]\r\n";
//		$header.= "X-Sender: ".($this->_from_name ? HTMLHelper::escape($this->_from_name).' ' : "")."<{$this->_from_email}>\r\n";

		if(!empty($this->_to)) {
			
			$to_strings = array();
			
			foreach($this->_to as $rcpt) {
				$to_strings[] = ($rcpt['name'] ? HTMLHelper::escape($rcpt['name']).' ' : "")." <{$rcpt['email']}>";
			}
			
			$header.= 'To: '.implode(', ', $to_strings)."\r\n";
			
		}
		
		if($this->_subject) $header.= "Subject: =?UTF-8?B?".(base64_encode($this->_subject))."?=\r\n";

		//------------------------------------------------------------------------------------------------------------------------------------
		
		$text_plain_header = 'Content-Type: text/plain' . ($this->_text_plain_charset ? "; charset=\"{$this->_text_plain_charset}\"" : '');
		$text_html_header = 'Content-Type: text/html' . ($this->_text_html_charset ? "; charset=\"{$this->_text_html_charset}\"" : '');
		
		
		if($text_plain && $text_html) {
			
			$text_boundary = '---=-t' . md5(uniqid()) . '--';
			$text_part = "Content-Type: multipart/alternative; boundary=\"{$text_boundary}\"\r\n\r\n".
						 "--{$text_boundary}\r\n{$text_plain_header}\r\n\r\n{$text_plain}\r\n\r\n".
						 "--{$text_boundary}\r\n{$text_html_header}\r\n\r\n{$text_html}\r\n\r\n".
						 "--{$text_boundary}--\r\n\r\n";
			
		} else if($text_html) $text_part = "{$text_html_header}\r\n\r\n{$text_html}\r\n\r\n";
		
		else $text_part = "{$text_plain_header}\r\n\r\n{$text_plain}\r\n\r\n";
					
		$total_size += strlen($text_part);
		
		//--------------------------------------------------------------------------------------------
		
		$header .= "Content-Length: {$total_size}\r\n";
		
		$this->_smtp_command("SIZE = {$total_size}");
		if($this->_smtp_command("DATA") != 354 ) return $this->_set_error('No se pudo comenzar el envio');
		
		$this->_smtp_send($header);
		
		//--------------------------------------------------------------------------------------------
		
		if(!empty($this->_embedded)) {
			$related_boundary = '---=-r' . md5(uniqid()) . '--';
			$this->_smtp_send("Content-Type: multipart/related; boundary=\"{$related_boundary}\"\r\n\r\n--{$related_boundary}\r\n");
		}
		
		if(empty($this->_attachments)) {
			$this->_smtp_send($text_part);
		} else {
			$mixed_boundary = '---=-m' . md5(uniqid()) . '--';
			$this->_smtp_send("Content-Type: multipart/mixed; boundary=\"{$mixed_boundary}\"\r\n\r\n");
			$this->_smtp_send("--{$mixed_boundary}\r\n{$text_part}");
			
			foreach($this->_attachments as $attachment) {
			
				$this->_smtp_send("--{$mixed_boundary}\r\n{$attachment['header']}\r\n\r\n");
				FilesHelper::file_transfer_base64($attachment['path'], $this->_smtp_connection_id);
				$this->_smtp_send("\r\n\r\n");
			}	
			
			$this->_smtp_send("--{$mixed_boundary}--\r\n\r\n");
		}
		
		if(!empty($this->_embedded)) {
			
			foreach($this->_embedded as $content) {
			
				$this->_smtp_send("--{$related_boundary}\r\n{$content['header']}\r\n\r\n");
				FilesHelper::file_transfer_base64($content['path'], $this->_smtp_connection_id);
				$this->_smtp_send("\r\n\r\n");
			}	
			
			$this->_smtp_send("--{$related_boundary}--\r\n\r\n");
		}

		//--------------------------------------------------------------------------------------------
		
		if($this->_smtp_send("\r\n.\r\n", true) != 250) return $this->_set_error('No se pudo enviar el Email');
		$this->_smtp_command('RSET');
		
		$this->_parsed_html = $text_html;
		$this->_parsed_text = $text_plain;

		return true;
	}

	//------------------------------------------------------------------------------------------------------------------------------------
	
		
	protected function _get_parsed_html() {
		return $this->_parsed_html;
	}
	
	protected function _get_parsed_text() {
		return $this->_parsed_text;
	}
	
	protected function _set_text_plain_charset($charset) {
		$this->_text_plain_charset = $charset;
	}
	
	protected function _set_text_html_charset($charset) {
		$this->_text_html_charset = $charset;
	}
	
	protected function _get_text_plain_charset() {
		return $this->_text_plain_charset;
	}
	
	protected function _get_text_html_charset() {
		return $this->_text_html_charset;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function set_text_charset($charset) {
		$this->_set_text_charset($charset);
	}
	
	public function get_text_charset() {
		return $this->_get_text_charset();
	}
	
	public function get_subject() {
		return $this->_subject;
	}

	public function set_subject($value) {
		$this->_subject = $value;
	}

	public function get_from_name() {
		return $this->_from_name;
	}

	public function set_from_name($value) {
		$this->_from_name = $value;
	}

	public function get_from_email() {
		return $this->_from_email;
	}

	public function set_from_email($value) {
		$this->_from_email = $value;
	}

	public function set_from($email, $name=null) {
		
		if(is_array($email)) {
			if(ArrayHelper::is_numeric($email)) {
				$_email = $email[0];
				$_name = $email[1];
			} else {
				$_email = $email['email'];
				$_name = $email['name'];
			}
			$this->set_from($_email, $_name);
		} else {
			if($email) {
				$this->set_from_email($email);
				$this->set_from_name($name);
			}
			
		}
		
	}

	public function get_reply_name() {
		return $this->_reply_name;
	}

	public function set_reply_name($value) {
		$this->_reply_name = $value;
	}

	public function get_reply_email() {
		return $this->_reply_email;
	}

	public function set_reply_email($value) {
		$this->_reply_email = $value;
	}

	public function set_reply($email, $name=null) {
		
		if(is_array($email)) {
			if(ArrayHelper::is_numeric($email)) {
				$_email = $email[0];
				$_name = $email[1];
			} else {
				$_email = $email['email'];
				$_name = $email['name'];
			}
			$this->set_reply($_email, $_name);
		} else {
			if($email) {
				$this->set_reply_email($email);
				$this->set_reply_name($name);
			}
		}
		
	}
	
	public function get_smtp_server() {
		return $this->_smtp_server;
	}

	public function set_smtp_server($value) {
		$this->_smtp_server = $value;
	}

	public function get_smtp_protocol() {
		return $this->_smtp_protocol;
	}

	public function set_smtp_protocol($value) {
		$this->_smtp_protocol = $value;
	}

	public function get_smtp_port() {
		return $this->_smtp_port;
	}

	public function set_smtp_port($value) {
		$this->_smtp_port = $value;
	}

	public function get_smtp_ssl() {
		return $this->_smtp_ssl;
	}

	public function set_smtp_ssl($value) {
		$this->_smtp_ssl = $value;
	}

	public function get_smtp_user_login() {
		return $this->_smtp_user_login;
	}

	public function set_smtp_user_login($value) {
		$this->_smtp_user_login = $value;
	}

	public function get_smtp_user_pass() {
		return $this->_smtp_user_pass;
	}

	public function set_smtp_user_pass($value) {
		$this->_smtp_user_pass = $value;
	}
	
	public function add_to($email, $name=null) {
		
		if(is_array($email)) {
			if(ArrayHelper::is_numeric($email)) {
				$_email = $email[0];
				$_name = $email[1];
			} else {
				$_email = $email['email'];
				$_name = $email['name'];
			}
			$this->add_to($_email, $_name);
		} else {
			if($email) {
				foreach(explode(self::EMAIL_SEPARATOR, $email) as $e)
				{
					$this->_to[] = array('email' => trim($e), 'name' => trim($name));
				}
			}
		}
	}
	
	public function clear_to() {
		$this->_to = array();
	}
	
	public function set_to($email, $name=null) {
		$this->clear_to();
		$this->add_to($email, $name);
	}
	
	public function add_cc($email, $name=null) {
		
		if(is_array($email)) {
			if(ArrayHelper::is_numeric($email)) {
				$_email = $email[0];
				$_name = $email[1];
			} else {
				$_email = $email['email'];
				$_name = $email['name'];
			}
			$this->add_cc($_email, $_name);
		} else {
			if($email) {
				foreach(explode(self::EMAIL_SEPARATOR, $email) as $e)
				{
					$this->_cc[] = array('email' => trim($e), 'name' => trim($name));
				}
			}
		}
	}
	
	public function clear_cc() {
		$this->_cc = array();
	}
	
	public function set_cc($email, $name=null) {
		$this->clear_cc();
		$this->add_cc($email, $name);
	}
	
	public function add_cco($email, $name=null) {
		
		if(is_array($email)) {
			if(ArrayHelper::is_numeric($email)) {
				$_email = $email[0];
				$_name = $email[1];
			} else {
				$_email = $email['email'];
				$_name = $email['name'];
			}
			$this->add_cco($_email, $_name);
		} else {
			if($email) {
				foreach(explode(self::EMAIL_SEPARATOR, $email) as $e)
				{
					$this->_cco[] = array('email' => trim($e), 'name' => trim($name));
				}
			}
		}
	}
	
	public function clear_cco() {
		$this->_cco = array();
	}
	
	public function set_cco($email, $name=null) {
		$this->clear_cco();
		$this->add_cco($email, $name);
	}

	
	public function add_attachment($path, $filename=null, $mimetype=null) {
		
		if(file_exists($path)) {
			
			if(!$filename) $filename = basename($path);
			
			$attachment = array();
			$attachment['content_size'] = (integer) @ filesize($path);
			$attachment['mimetype'] = $mimetype ? $mimetype : MimeTypeHelper::from_filename($path);
			$attachment['filename'] = $filename;
			$attachment['path'] = $path;
			$attachment['header'] = "Content-Type: {$attachment['mimetype']}; name=\"{$filename}\"\r\nContent-Transfer-Encoding: base64\r\nContent-Length: {$attachment['content_size']}\r\nContent-Disposition: attachment; filename=\"{$filename}\"";
			$attachment['header_size'] = strlen($attachment['header']);
			$attachment['total_size'] = $attachment['header_size'] + $attachment['content_size']; 
								
			$this->_attachments[$filename] = $attachment;
		}
	}
	
	public function remove_attachment($filename) {
		$filename = basename($filename);
		if(array_key_exists($filename, $this->_attachments)) {
			unset($this->_attachments[$filename]);
		}
	}
	
	public function clear_attachments() {
		$this->_attachments = array();
	}
	
	public function add_embedded($path, $filename=null, $mimetype=null) {
		
		if(file_exists($path)) {
			
			if(!$filename) $filename = basename($path);
			
			$attachment = array();
			$attachment['content_size'] = (integer) @ filesize($path);
			$attachment['mimetype'] = $mimetype ? $mimetype : MimeTypeHelper::from_filename($path);
			$attachment['filename'] = $filename;
			$attachment['path'] = $path;
			$attachment['header'] = "Content-Type: {$attachment['mimetype']}; name=\"{$filename}\"\r\nContent-Transfer-Encoding: base64\r\nContent-Length: {$attachment['content_size']}\r\nContent-Location: {$filename}";
			$attachment['header_size'] = strlen($attachment['header']);
			$attachment['total_size'] = $attachment['header_size'] + $attachment['content_size']; 
								
			$this->_embedded[$filename] = $attachment;
		}
	}
	
	public function remove_embedded($filename) {
		$filename = basename($filename);
		if(array_key_exists($filename, $this->_embedded)) {
			unset($this->_embedded[$filename]);
		}
	}
	
	public function clear_embedded() {
		$this->_embedded = array();
	}
	
	public function get_parsed_content() {
		return $this->_get_parsed_content();
	}


	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function has_error() {
		return !is_null($this->_error);
	}
	
	public function get_error() {
		return $this->_error;
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function send($email=null, $name=null) {
		
		$num_args = func_num_args();
		
		if($num_args == 0) {
			
			if(!ZPHP::get_config('email_send', true)) return true;

			$original_to = array_merge($this->_to, array());
			$original_cc = array_merge($this->_cc, array());
			$original_cco = array_merge($this->_cco, array());

			$force_cc_emails = array_filter((array) ZPHP::get_config('email_force_cc_email'));
			$force_cco_emails = array_filter((array) ZPHP::get_config('email_force_cco_email'));

			$force_cc_names = array_filter((array) ZPHP::get_config('email_force_cc_name'));
			$force_cco_names = array_filter((array) ZPHP::get_config('email_force_cco_name'));

			foreach($force_cc_emails as $index => $email) {
				$this->add_cc($email, isset($force_cc_names[$index]) ? $force_cc_names[$index] : null);
			}

			foreach($force_cco_emails as $index => $email) {
				$this->add_cco($email, isset($force_cco_names[$index]) ? $force_cco_names[$index] : null);
			}

			$this->_clear_error();
			
			$force_to_emails = array_filter((array) ZPHP::get_config('email_force_to_email'));
			
			if(!empty($force_to_emails))
			{
				$this->_to = array();
				$force_to_names = (array) ZPHP::get_config('email_force_to_name');
				
				foreach($force_to_emails as $index => $email) {
					$this->add_to($email, isset($force_to_names[$index]) ? $force_to_names[$index] : null);
				}	
			}

			$response = $this->_send();
			
			$this->_to = $original_to;
			$this->_cc = $original_cc;
			$this->_cco = $original_cco;
			
			return $response;
			
		} else {
			
			$original_to = array_merge($this->_to, array());
			$this->set_to($email, $name);
			$result = $this->send();
			$this->_to = $original_to;
			return $result;
		}
		
	}
	
	public function send_to($email, $name=null) {
		return $this->send($email, $name);
	}
}