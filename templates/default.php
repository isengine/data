<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

$instance = $object -> get('instance');
$template = $object -> get('template');

//$object -> eget('container') -> addClass('new');
//$object -> eget('container') -> open(true);
//$object -> eget('container') -> close(true);
//$object -> eget('container') -> print();

?>

<div class="<?= $instance; ?>">
	
	<?php
		$object -> reverseData();
		$object -> read($template, 10);
	?>
	
</div>
