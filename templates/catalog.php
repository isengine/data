<?php

namespace is\Masters\Modules\Isengine\Content;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;
use is\Helpers\Prepare;

$instance = $this->get('instance');

//$this->eget('container')->addClass('new');
//$this->eget('container')->open(true);
//$this->eget('container')->close(true);
//$this->eget('container')->print();

//$this->data->countMap(true);

//System::debug($this->map->getData());
//System::debug($this->settings);

$print = [
    'desktop_main' => null,
    'desktop_sub' => null,
    'mobile' => null
];

$link_array = ['catalog'];

?>
<div class="row accordion" id="accordionNav">
    <div class="col-12 d-block">
<?php
$this->iterate(function($data) use (&$print, $this, &$link_array){
    /*
    * Каталог для мобильной версии
    * все элементы меню вместе
    */

    $key = Strings::join($data['id'], '-');

    $translit = $this->translit(Strings::replace(Prepare::lower(Prepare::words($data['title'])), ' ', '-'), 'en', 'ru');
    $link_array[] = $translit;
    $link = $data['link'] ? '/catalog/' . ($data['link'] === true ? $translit : $data['link']) . '/' : '/' . Strings::join($link_array, '/') . '/';

    $print['mobile'] .= '
    <div class="accordion-item">
        <div class="accordion-header my-1 " id="accordion-' . $key . '">
    ';

    if ($data['iterable']) {
        $print['mobile'] .= '
            <button class="btn p-0 collapsed d-flex justify-content-between align-items-center w-100" type="button" data-bs-toggle="collapse" data-bs-target="#accordionCollapse-' . $key . '" aria-expanded="false" aria-controls="accordionCollapse-' . $key . '">
                <span class="color-nav px-1 text-start">
                    ' . ($data['class'] ? '<i class="' . $data['class'] . '"></i>' : null) . '
                    ' . Prepare::upperFirst($data['title']) . '
                </span>
                <i class="bi-chevron-right rotate"></i>
            </button>
        ';
    } else {
        $print['mobile'] .= '
            <a href="' . $link . '" class="color-nav p-1 d-block text-start">
                ' . $data['title'] . '
            </a>
        ';
    }

    $print['mobile'] .= '
        </div>
    ';

    if ($data['iterable']) {
        $print['mobile'] .= '
        <div id="accordionCollapse-' . $key . '" class="accordion-collapse collapse accordion-mobile" aria-labelledby="accordion-' . $key . '">
            <a href="' . $link . '" class="color-nav p-1 d-block">
                Показать товары
            </a>
            <div class="accordion-body p-0">
        ';
    }
}, function($data) use (&$print, &$link_array){
    $link_array = Objects::unlast($link_array);

    if ($data['iterable']) {
        $print['mobile'] .= '
            </div>
        </div>
        ';
    }

    $print['mobile'] .= '
    </div>
    ';
});
?>
        <?= $print['mobile']; ?>
    </div>
</div>