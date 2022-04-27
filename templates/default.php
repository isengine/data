<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

$instance = $this->get('instance');
$template = $this->get('template');

//$this->eget('container')->addClass('new');
//$this->eget('container')->open(true);
//$this->eget('container')->close(true);
//$this->eget('container')->print();

?>

<div class="<?= $instance; ?>">

    <?php
        $this->reverseData();
        $this->iterate(function($item){
    ?>
        <div>
            <?php System::debug($item, '!q'); ?>
        </div>
    <?php
        });
    ?>

</div>
