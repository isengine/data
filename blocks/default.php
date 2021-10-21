<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Masters\View;

$data = &$item['data'];

$view = View::getInstance();

?>
<div class="item">
	<?= $data['title']; ?>
</div>
<?php if ($item['cookie']) { ?>
<script>
	<?= $item['cookie']; ?>
</script>
<?php }; ?>