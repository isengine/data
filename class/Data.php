<?php

namespace is\Masters\Modules\Isengine;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Local;
use is\Helpers\Datetimes;
use is\Helpers\Sessions;

use is\Components\Collection;
use is\Components\Datetime;

use is\Masters\View;

use is\Masters\Modules\Master;

class Data extends Master {
	
	public $format; // входящий формат даты и времени
	public $datetime; // объект Datatime
	
	public $tvar; // объект обработчика текстовых переменных
	public $translit; // объект транслитирования строк
	
	public $custom; // массив настроек, содержит названия элементов, см.метод names
	
	/*
	* Этот класс является универсальным классом для работы с любыми данными,
	* включая обход вложенных массивов с рекурсией
	* 
	* Когда мы делали этот класс, мы старались одновременно
	* максимально упростить работу с данными,
	* автоматизировать ее
	* и кастомизировать
	* 
	* благодаря этому, работа по подготовке данных ушла в шаблон
	* из которого можно вызывать блоки в качестве отдельных элементов итерации
	*/
	
	public function collection($data = null) {
		// создает из данных коллекцию
		// это значит, что данные можно будет сортировать, фильтровать и прочее
		$this -> data = new Collection;
		// для работы использовать $this -> collection($data)
		// если пропустить $data, будет подставлен $this -> settings
		$this -> data -> addByList($data ? $data : $this -> settings);
	}
	
	public function tvars(&$item) {
		// вызывает обработку текстовых переменных для элемента
		$item = $this -> tvars -> launch($item);
	}
	
	public function translit($item, $to = null, $from = null) {
		// вызывает транслитирование строки
		return $this -> translit -> launch($item, $to, $from);
	}
	
	public function datetime($item, $format = null) {
		// возвращает дату в нужном формате
		// иначе формат даты остается без преобразований
		// если item = null, вернет текущую метку
		return $this -> datetime -> convertDate($item, null, $format);
	}
	
	public function datetimeFormat($format) {
		// задает входящий формат для даты и времени
		$this -> format = $format;
		$this -> datetime -> setFormat($format);
	}
	
	public function datetimeCompare($ctime = null, $dtime = null) {
		// проверка даты, возвращает результат
		// если результат отрицательный, значит дата находится в заданных рамках
		// если результат 1 или -1, то данные можно пропустить
		return $this -> datetime -> compareDate($ctime, $dtime, $this -> format);
	}
	
	public function cookie($cookie) {
		
		// метод проверяет куку в сессии по заданному имени
		// если куки нет, возвращает код ее добавления в скрипт
		// если кука есть, то возвращает пустое значение null
		// передается один аргумент в виде массива
		//   name - имя куки, обычно $instance и имя материала
		//   time - время жизни куки,
		// в секундах или спец.формате, например '1:hour' или '3600'
		
		$time = time();
		$c_ses = Sessions::getCookie('is-expired-' . $cookie['name']);
		$c_set = Datetimes::amount($cookie['time']);
		
		// проверка куки, возвращает результат
		return ($c_set > 0 && $c_ses + $c_set <= $time) ? 'document.cookie = "is-expired-' . $cookie['name'] . '=' . $time . 'path=/; max-age=' . $c_set . '";' : null;
		
	}
	
	public function names($array = null) {
		
		// служебный метод вывода массива служебных ключей
		// id - для хранения идетнификатора, с учетом вложенности
		// например 0, 0:1, 0:1:0, 2:2:10 и т.д.
		// level - для хранения уровня вложенности
		// format - для входящего формата даты и времени
		// ctime - для времени создания материала, в заданном формате
		// dtime - для времени удаления материала, в заданном формате
		// cookie - массив, содержащий ключи
		//   name - для имени куки
		//   time - для времени хранения куки
		// iterable - триггер вложенности данных, т.е. имеет ли смысл их итерировать
		// data - для хранения массива вложенных данных
		
		return ['id', 'level', 'format', 'ctime', 'dtime', 'cookie', 'iterable', 'data'];
		
	}
	
	public function custom($array = null, $val = null) {
		
		// метод заполнения массива ключей, см.метод names
		// например, ['id' => 'sku', 'level' => 'inside', ...]
		// для получения стандартных значений, используйте
		// $this -> custom();
		// чтобы задать отдельное значение, используйте
		// $this -> custom(key, value);
		
		$names = $this -> names();
		
		if (!$array && !$val) {
			$this -> custom = Objects::join($names, $names);
		} elseif (is_array($array)) {
			foreach ($names as $item) {
				$this -> custom[$item] = $array[$item];
			}
			unset($item);
		} else {
			$this -> custom[$array] = $val;
		}
		
	}
	
	public function launch() {
		
		if (!System::typeIterable($this -> settings)) {
			return true;
		}
		
		$this -> custom();
		
		$this -> setData( $this -> settings );
		
		$this -> datetime = Datetime::getInstance();
		// для работы обязательно запустить datetimeFormat(...)
		// например, $this -> datetimeFormat($item['format']);
		
		$view = View::getInstance();
		$this -> tvars = $view -> get('tvars');
		$this -> translit = $view -> get('translit');
		unset($view);
		// для работы использовать $this -> tvars(...)
		// например, $this -> tvars($item['title']);
		
		// мы сейчас не будем останавливаться
		// на автоматизации структуры и подсчетах
		// и нам незачем сейчас читать базу данных
		// мы пока выключим все возможности, кроме базовой
		
		/*
		$this -> structure = $this -> settings['structure'];
		
		$this -> data = new Collection;
		
		$this -> read();
		$this -> sort($this -> settings['sort']);
		$this -> limit($this -> settings['skip'], $this -> settings['limit']);
		
		// подсчет не точный,
		// т.к. один и тот же товар в разных категориях
		// будет считать каждый раз,
		// хотя так складывать неправильно
		// но алгоритм подсчета класса map в этом не виноват,
		// а виновата выборка в методе counter этого класса
		$this -> data -> countMap(true);
		//$this -> data -> countMap();
		
		$this -> count = &$this -> data -> map -> count;
		$this -> total = &$this -> data -> map -> total;
		*/
	}
	
	public function iterate($callback, $callback_after = null, $array = null, $recursive = true) {
		
		/*
		* метод для запуска из-под шаблона в виде:
		* iterate(function($data){
		*   ...
		* });
		* 
		* тест:
		* $this -> iterate(function($data){
		*   System::debug($data);
		* });
		* 
		* пример:
		* <?php $this -> iterate(function($data){ ?>
		*   <div class="item">
		*     <a href="<?= $data['link']; ?>">
		*       <p><?= $data['name']; ?></p>
		*     </a>
		*   </div>
		* <? }); ?>
		*/
		
		// здесь мы задаем массив данных по-умолчанию
		$data = $array ? $array[ $this -> custom['data'] ] : $this -> settings;
		
		// здесь мы отменяем итерацию, если нечего итерировать
		if (!is_array($data)) {
			return;
		}
		
		// здесь мы задаем базовые значения
		$return = null;
		$names = Objects::remove($this -> names(), 'data');
		
		// здесь берем значения из кастомных ключей
		// и присваиваем их переменным со стандартными именами
		// (раньше было массиву со стандартными ключами)
		foreach ($names as $item) {
			$val = $array[ $this -> custom[$item] ];
			$$item = System::set($val) ? $val : null;
		}
		unset($item);
		
		if ($id === null) {
			$id = [];
		}
		if ($level === null) {
			$level = -1;
		}
		
		$level++;
		
		$target = [
			Objects::first($data, 'key'),
			Objects::last($data, 'key'),
			Objects::len($data)
		];
		
		foreach ($data as $key => $item) {
			
			$id[$level] = $key;
			$iterable = System::typeIterable($item[ $this -> custom['data'] ]);
			
			foreach ($names as $i) {
				$c = &$item[ $this -> custom[$i] ];
				if ($$i) {
					$c = $$i;
				} elseif ($c) {
					$$i = $c;
				}
				unset($c);
			}
			unset($i);
			
			// проверка куки, если они заданы
			if ($cookie['name'] && $cookie['time']) {
				$cookies = $this -> cookie($cookie);
				if (!$cookies) {
					continue;
				}
				$item[ $this -> custom['cookie'] ] = $cookies;
				unset($cookies);
			}
			
			// устанавливаем формат дат, если он задан
			if ($format) {
				$this -> datetimeFormat($format);
			}
			
			// проверка дат, если они заданы
			if (
				($ctime || $dtime) &&
				!$this -> datetimeCompare($ctime, $dtime)
			) {
				continue;
			}
			
			// задаем позицию элемента
			$position = null;
			if ($target[2] === 1) {
				$position = 'alone';
			} elseif ($key === $target[0]) {
				$position = 'first';
			} elseif ($key === $target[1]) {
				$position = 'last';
			}
			
			// запуск функции вывода 'до'
			$return .= call_user_func($callback, $item, $key, $position);
			
			if ($recursive && $iterable) {
				// запуск внутренней итерации
				$this -> iterate($callback, $callback_after, $item, $recursive);
			}
			
			// запуск функции вывода 'после'
			if ($callback_after) {
				$return .= call_user_func($callback_after, $item, $key, $position);
			}
			
		}
		unset($item);
		
		$level--;
		
		return $return;
		
	}
	

}

?>