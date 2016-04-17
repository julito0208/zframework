<?php

include_once ZPHP::get_zframework_dir().'/core/thirdparty/solrclient/solrclient.php';

class SolrConnection extends SmartObject
{
	
	protected static $_connections = array();
	
	const DEFAULT_HOSTNAME = 'localhost';
	const DEFAULT_PORT = 8983;
	
	/* @return SolrConnection */
	public static function get_connection($host=null, $port=null, $url_index=null, $core=null)
	{
		if(func_num_args() == 0)
		{
			return self::get_default_connection();
		}
		
		if(!$host)
		{
			$host = self::DEFAULT_HOSTNAME;
		}
		
		if(!$port)
		{
			$port = self::DEFAULT_PORT;
		}
		
		$key = var_export($host, true).var_export($port, true).var_export($url_index, true).var_export($core, true);
		
		if(!isset(self::$_connections[$key]))
		{
			$connection = new SolrConnection($host, $port, $url_index, $core);
			self::$_connections[$key] = $connection;
		}
		
		return self::$_connections[$key];
		
	}
	
	/* @return SolrConnection */
	public static function get_default_connection()
	{
		return self::get_connection(
			ZPHP::get_config('solr_hostname'),
			ZPHP::get_config('solr_port'),
			ZPHP::get_config('solr_url_prefix'),
			ZPHP::get_config('solr_core')
		);
	}

	
	public static function get_result_rows(array $result, $rows_limit=0)
	{
		$rows = array();
		
		$array_key = false;
		
		foreach($result as $key => $value)
		{
			if(is_array($value))
			{
				$array_key = true;
				
				$result_row = array_merge($result, array());
				
				foreach($value as $array_value)
				{
					$result_row[$key] = $array_value;
					$rows = array_merge($rows, self::get_result_rows($result_row));
					
					if($rows_limit && count($rows) >= $rows_limit)
					{
						break;
					}
				}
				
			}
			
			if($rows_limit && count($rows) >= $rows_limit)
			{
				break;
			}
		}
		
		if(!$array_key)
		{
			$rows[] = $result;
		}
		
		return $rows;
	}
	
	/*-----------------------------------------------------------------*/
	
	
	protected static function _parse_query($query, $return_joint=true, $can_empty=false)
	{
		$params = array();
		
		foreach(CastHelper::to_array($query) as $key => $value)
		{
			if(is_numeric($key))
			{
				if(is_array($value))
				{
					$params[] = '('.implode(' OR ', self::_parse_query($value, false, true)).')';
				}
				else
				{
					$params[] = $value;
				}
			}
			else
			{
				if(is_array($value))
				{
					$group_conditions = array();
					
					foreach($value as $k => $v)
					{
						$group_conditions[] = "{$key}:\"".StringHelper::escape($v)."\"";
					}
					
					$params[] = '('.implode(' OR ', $group_conditions).')';
				}
				else
				{
					$params[] = "{$key}:\"".StringHelper::escape($value)."\"";
				}
			}
		}
		
		if(!$can_empty && empty($params))
		{
			$params[] = '*:*';
		}
		
		if($return_joint)
		{
			return implode(' AND ', $params);
		}
		else
		{
			return $params;
		}
	}
	
	
	/*-----------------------------------------------------------------*/
	
	protected $_host;
	protected $_port;
	protected $_url_prefix;
	protected $_core;
	
	protected $_request_handler;
	
	protected $_search_results = array();
	protected $_search_count = 0;

	protected function __construct($host, $port, $url_prefix, $core)
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_url_prefix = $url_prefix;
		$this->_core = $core;
		
		$url_prefix = trim($this->_url_prefix, ' /');
		$core = trim($this->_core, ' /');

		$request_handler = '';
		
		if($url_prefix)
		{
			$request_handler.= '/'.$url_prefix;
		}
		
		if($core)
		{
			$request_handler.= '/'.$core;
		}
		
		$this->_request_handler = $request_handler;
		
	}
	
	/*------------------------------------------------------------------------------------------------*/
	
	public function __get($name)
	{
		$callable = array($this, 'get_'.$name);
		
		if(is_callable($callable))
		{
			return call_user_func($callable);
		}
		else
		{
			return null;
		}
	}
	
	protected function _get_request_url($action, $complete=true)
	{
		$url = $this->_request_handler.'/'.trim($action, ' /');
		
		if($complete)
		{
			$url = 'http://'.$this->_host.':'.$this->_port.'/'.ltrim($url, '/');
		}
		
		return $url;
	}
	
	
	/*------------------------------------------------------------------------------------------------*/
	
	public function get_host() 
	{
		return $this->_host;
	}

	public function get_url_prefix() 
	{
		return $this->_url_prefix;
	}

	public function get_core() 
	{
		return $this->_core;
	}

	public function get_port() 
	{
		return $this->_port;
	}
	
	public function get_search_results() 
	{
		$rows = $this->_search_results;

//		$rows = array();
//		$count = count($this->_search_results);
//		
//		foreach($this->_search_results as $result)
//		{
//			$rows = array_merge($rows, self::get_result_rows($result, $count));
//		}
//		

		return $rows;
	}

	public function get_search_count() 
	{
		return $this->_search_count;
	}

	
	/*------------------------------------------------------------------------------------------------*/

	public function search($search_params=array(), $limit=null, $order=null)
	{
		$params = array();
		$params['q'] = self::_parse_query($search_params);
		
		
		if(!is_null($limit)) {
			
			if(!is_array($limit)) {
				
				$params['rows'] = (integer) $limit;
				$params['start'] = 0;
				
			}
			else 
			{
				
				if(array_key_exists('page', $limit)) {
					
					$page = ArrayHelper::pop_value($limit, 'page');
					
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					$start = ((integer) $page) * $length;
				
				} else {
					
					$start = ArrayHelper::pop_value($limit, array('start', 'pos', 0, 1));
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					
				}
				
				$params['start'] = $start;
				$params['rows'] = $length;
				
			}
			
		}
		
		if($order)
		{
			$params['order'] = $order;
		}
		
		$params['wt'] = 'json';
		
		$url = $this->_get_request_url('select');
		$url.= '?'.  http_build_query($params);
		
		$contents = file_get_contents($url);
		
		$data = JSONMap::unserialize($contents);

		$this->_search_count = $data['response']['numFound'];
		$this->_search_results = $data['response']['docs'];
		
		return $this->get_search_count();
		
	}

	public function delete($search_params=array())
	{
		$search_params = self::_parse_query($search_params, false, true);
		
		if(empty($search_params))
		{
			return 0;
		}
		
		$stream_body = "";
		
		foreach($search_params as $value)
		{
			$stream_body.= '<query>'.HTMLHelper::escape($value).'</query>';
		}
		
		$params = array();
		$params['commit'] = 'true';
		$params['stream.body'] = '<delete>'.$stream_body.'</delete>';

		$url = $this->_get_request_url('update');
		$url.= '?'.  http_build_query($params);
		
		$contents = file_get_contents($url);

		return $contents;
	}

	public function insert($data, $replace=false)
	{
		$data = CastHelper::to_array($data);
		
		if($replace)
		{
			$this->delete($data);
		}
		
		$json = json_encode(array($data));
		var_export($data);
		$url = $this->_get_request_url('update');
		$url.= '?replacefields=false';
		
		$ch = curl_init($url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($json))                                                                       
		);                                                                                                                   

		$result = curl_exec($ch);

		return $result;
		$search_params = (array) $search_params;
		$args = array();
		
		foreach($search_params as $key => $value)
		{
//			$search_params[$key] = '<field name="'.HTMLHelper::escape($key).'">'.HTMLHelper::escape($value).'</field>';
			$args[] = "<field name=\"".HTMLHelper::escape($key)."\">".HTMLHelper::escape($value)."</field>";
		}
		
//		$xml = '<add><doc>'.implode('', array_values($args)).'</doc></add>';
		
		
		$xml = '<doc>'.implode('', array_values($args)).'</doc>';
		$xml = "<add>{$xml}</add>";
		$xml = '<?xml version="1.0" encoding="iso-8859-15"?>'."\n\n".$xml;
		
		
		$xml_file = FilesHelper::file_create_temp($xml);

		$url = $this->_get_request_url('update', true);
		$url = $url.'?commit=true';
		
		$url = 'http://ztock.julio?f=1';
		
		$request = curl_init($url);

		// send a file
		curl_setopt($request, CURLOPT_POST, true);
		curl_setopt(
			$request,
			CURLOPT_POSTFIELDS,
			array(
				'file' => '@'.realpath($xml_file)
			));
		curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
		// output the response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		var_export(curl_exec($request));

		// close the session
		curl_close($request);
		
//		$this->_create_solr_client('update');
//		foreach($params as $key => $arg)
//		{
//			$this->_solr_client->addParameter($key, $arg);
//		}
//		
//		$this->_solr_client->doQuery();
		
		return $this->get_search_count();
	}
	
	public function replace($data)
	{
		return $this->insert($data, true);
	}
}
