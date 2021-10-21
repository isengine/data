<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Masters\View;

$view = View::getInstance();

$print = null;

$this -> iterate(function($item) use (&$print, &$view){
?>
<!-- Modal -->
<div class="modal fade" id="modal-<?= $item['name']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?= $item['name']; ?>" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel-<?= $item['name']; ?>"><?= $item['title']; ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<?= $item['description']; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $view -> get('lang|common:close'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php
	$print .= 'window.onload = function(e){';
	if ($item['delay']) {
		$print .= 'setTimeout(function(){';
	}
	$print .= '
		new bootstrap.Modal(document.getElementById("modal-' . $item['name'] . '")).show();
		var modal_' . $item['name'] . ' = document.getElementById("modal-' . $item['name'] . '");
		modal_' . $item['name'] . '.addEventListener("hidden.bs.modal", function (event) {' . $item['cookie'] . '});
	';
	if ($item['delay']) {
		$print .=  '}, ' . (((float) $item['delay']) * 1000) . ');';
	}
	$print .=  '}';
}, null, null, null);

echo $print ? '<script>' . $print . '</script>' : null;

?>