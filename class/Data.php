<?php

namespace is\Masters\Modules\Isengine;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Sessions;
use is\Helpers\Datetimes;

use is\Masters\Modules\Master;
use is\Masters\View;

use is\Components\Datetime;

class Data extends Master {
	
	public $format;
	public $time;
	public $dt;
	
	public function launch() {
		$this -> dt = Datetime::getInstance();
		$this -> time = time();
		if (!System::typeIterable($this -> settings)) {
			return;
		}
	}
	
	public function format($format) {
		$this -> format = $format;
	}
	
	public function random() {
		$this -> settings = Objects::random($this -> settings);
	}
	
	public function reverse() {
		$this -> settings = Objects::reverse($this -> settings);
	}
	
	public function select($offset = null, $len = null) {
		$this -> settings = Objects::get($this -> settings, $offset, $len);
	}
	
	public function read($name = 'default', $limit = null) {
		
		if (!System::typeIterable($this -> settings)) {
			return;
		}
		
		$count = 0;
		
		foreach ($this -> settings as $key => $item) {
			
			$data = $item['data'];
			if (
				!System::typeIterable($item) ||
				!System::typeIterable($data)
			) {
				continue;
			}
			
			if ($this -> compareDate($item)) {
				if (!$this -> compareCookie($item)) {
					$item['cookie'] = $this -> scriptCookie($item);
					if ( !System::includes($name, $this -> custom . 'blocks', null, $item) ) {
						System::includes($name, $this -> path . 'blocks', null, $item);
					}
				}
			}
			
			if ($limit) {
				$count++;
				if ($count >= $limit) {
					break;
				}
			}
			
		}
		unset($key, $item);
		
	}
	
	public function compareDate($item) {
		return $this -> dt -> compareDate($item['ctime'], $item['dtime'], $item['format'] ? $item['format'] : $this -> format);
	}
	
	public function compareCookie(&$item) {
		$item['cookie'] = $item['cookie'] ? Datetimes::amount($item['cookie']) : null;
		$cookie = Sessions::getCookie('informer-' . $item['name']);
		$cookie = $cookie + $item['cookie'] > $this -> time;
		return $cookie;
	}
	
	public function scriptCookie($item) {
		return $item['cookie'] ? 'document.cookie = "informer-' . $item['name'] . '=' . $this -> time . 'path=/; max-age=' . $item['cookie'] . '";' : null;
	}
	
}

?>