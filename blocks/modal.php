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
<!-- Modal -->
<div class="modal fade" id="modal-<?= $item['name']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?= $item['name']; ?>" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel-<?= $item['name']; ?>"><?= $data['title']; ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<?= $data['description']; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $view -> get('lang|common:close'); ?></button>
			</div>
		</div>
	</div>
</div>
<script>
window.onload = function(e){ 
<?php if ($item['delay']) { ?>
	setTimeout(function(){
<?php } ?>
	new bootstrap.Modal(document.getElementById('modal-<?= $item['name']; ?>')).show();
	var modal_<?= $item['name']; ?> = document.getElementById('modal-<?= $item['name']; ?>');
	modal_<?= $item['name']; ?>.addEventListener('hidden.bs.modal', function (event) {
<?php
	if ($item['cookie']) {
		echo $item['cookie'];
	}
?>
	});
<?php
	if ($item['delay']) {
?>
	}, <?= ((float) $item['delay']) * 1000; ?>);
<?php } ?>
};
</script>