<?php

class CacheSystemNone implements CacheSystem {
	
	
	public function save($key, $value) {}
	
	public function delete($key) {}
	
	public function get($key) {
		return null;
	}
	
	public function exists($key) {
		return false;
	}
	
}