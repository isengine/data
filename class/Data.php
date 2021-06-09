<?php

namespace is\Masters\Modules\Isengine;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Masters\Modules\Master;
use is\Masters\View;

use is\Components\Datetime;

class Data extends Master {
	
	public $format;
	
	public function launch() {
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
	
	public function read($name, $limit = null) {
		
		$dt = Datetime::getInstance();
		$count = 0;
		
		foreach ($this -> settings as $key => $item) {
			
			$data = $item['data'];
			if (
				!System::typeIterable($item) ||
				!System::typeIterable($data)
			) {
				continue;
			}
			
			$result = $dt -> compareDate($item['ctime'], $item['dtime'], $item['format'] ? $item['format'] : $this -> format);
			
			if ($result) {
				if ( !System::includes($name, $this -> custom . 'blocks', null, $item) ) {
					System::includes($name, $this -> path . 'blocks', null, $item);
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
	
}

?>