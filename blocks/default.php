<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Masters\View;

$item = &$object;
$data = &$object['data'];

$view = View::getInstance();

?>
<div class="item">
	<?= $data['title']; ?>
</div>