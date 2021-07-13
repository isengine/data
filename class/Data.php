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
	
	public $processing;
	public $format;
	public $time;
	public $dt;
	
	public function launch() {
		$this -> dt = Datetime::getInstance();
		$this -> time = time();
		if (!System::typeIterable($this -> settings)) {
			return;
		}
		$this -> processingPrepare();
		$this -> processingLaunch($this -> settings);
		$this -> processingExtract();
	}
	
	public function format($format) {
		$this -> format = $format;
	}
	
	public function read($name = 'default', $limit = null) {
		
		// проверяет куки, дату, преобразует языки и текстовые переменные
		// в итоге открывает шаблон вывода одиночного материала из блоков
		
		if (!System::typeIterable($this -> data)) {
			return;
		}
		
		$count = 0;
		
		foreach ($this -> getData() as $key => $item) {
			
			$data = $item['data'];
			if (
				!System::typeIterable($item) ||
				!System::typeIterable($data)
			) {
				continue;
			}
			
			if ($this -> compareDate($item)) {
				if (!$this -> compareCookie($item)) {
					// скрипт добавления куки в item['cookie']
					$item['cookie'] = $this -> scriptCookie($item);
					// загрузка шаблона из блоков
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
		// проверка даты, возвращает результат
		return $this -> dt -> compareDate($item['ctime'], $item['dtime'], $item['format'] ? $item['format'] : $this -> format);
	}
	
	public function compareCookie(&$item) {
		// проверка куки, возвращает результат
		$item['cookie'] = $item['cookie'] ? Datetimes::amount($item['cookie']) : null;
		$cookie = Sessions::getCookie('is-expired-' . $item['name']);
		$cookie = $cookie + $item['cookie'] > $this -> time;
		return $cookie;
	}
	
	public function scriptCookie($item) {
		// скрипт с записью куки
		return $item['cookie'] ? 'document.cookie = "is-expired-' . $item['name'] . '=' . $this -> time . 'path=/; max-age=' . $item['cookie'] . '";' : null;
	}
	
	public function processingExtract() {
		// извлекает данные в data модуля, а настройки оставляет без этих данных
		if ($this -> settings['data']) {
			$this -> setData( $this -> settings['data'] );
			unset( $this -> settings['data'] );
		} else {
			$this -> setData( $this -> settings );
			unset( $this -> settings );
		}
	}
	
	public function processingPrepare() {
		
		$view = View::getInstance();
		
		$this -> processing = [
			// определяет текущий язык и язык по-умолчанию
			'lang' => [
				$view -> get('state|lang'),
				$view -> get('state|langs:default')
			],
			// подготавливает обработчик текстовых переменных
			'tvars' => $view -> get('tvars')
		];
		
	}
	
	public function processingLaunch(&$data) {
		return Objects::each($data, function($item) {
			
			// нужно добавить в настройки преобразование языков "val" : {"ru" : "..."} -> "val" : "..."
			if (System::typeIterable($item)) {
				$keys = Objects::keys($item);
				$l_cur = $this -> processing['lang'][0];
				$l_def = $this -> processing['lang'][1];
				if (Objects::match($keys, $l_cur)) {
					$item = $item[ $l_cur ];
				} elseif (Objects::match($keys, $l_def)) {
					$item = $item[ $l_def ];
				} else {
					$this -> processingLaunch($item);
				}
			}
			
			// нужно добавить в настройки текстовые переменные
			if (
				System::type($item, 'string') &&
				Strings::match($item, '{') &&
				Strings::match($item, '}')
			) {
				$item = $this -> processing['tvars'] -> launch($item);
			}
			
			return $item;
			
		});
	}
	
}

?>