<?php

interface CacheSystem {

	public function get($key);

	public function exists($key);

	public function save($key, $value);
	
	public function delete($key);
	
}