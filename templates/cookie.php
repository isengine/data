<?php

namespace is\Masters\Modules\Isengine\Data;

use is\Helpers\System;
use is\Helpers\Objects;
use is\Helpers\Strings;

use is\Masters\View;

$view = View::getInstance();

$item = Objects::last($object -> settings, 'value');
$data = &$item['data'];

if (
	!System::typeIterable($item) ||
	!System::typeIterable($data)
) {
	return;
}

if (!$object -> compareDate($item)) {
	return;
}

if ($object -> compareCookie($item)) {
	return;
}

$item['cookie'] = $object -> scriptCookie($item);

?>
<div class="position-fixed bottom-0 m-4 p-4 bg-white border" id="block-<?= $item['name']; ?>"<?= $item['delay'] ? ' style="display:none;"' : null; ?>>
	<div class="row justify-content-center align-items-center">
		<div class="col-12 col-md col-xl-10 col-xxl-9 pb-2 pb-md-0">
			<?= $data['description'] !== true ? $data['description'] : $view -> get('lang|common:cookie'); ?>
		</div>
		<div class="col-auto">
<?php if ($data['link']) { ?>
			<a href="<?= $data['link']; ?>" class="btn btn-outline-primary">
				<?= $data['readmore'] !== true ? $data['readmore'] : $view -> get('lang|common:readmore'); ?>
			</a>
<?php } ?>
			<button type="button" class="btn btn-primary" id="button-<?= $item['name']; ?>">
				<?= $data['agree'] !== true ? $data['agree'] : $view -> get('lang|common:agree'); ?>
			</button>
		</div>
	</div>
</div>
<script>
<?php if ($item['cookie']) { ?>
let button_<?= $item['name']; ?> = document.getElementById('button-<?= $item['name']; ?>'); 
button_<?= $item['name']; ?>.onclick = function() {
	<?= $item['cookie']; ?>
	let block_<?= $item['name']; ?> = document.getElementById('block-<?= $item['name']; ?>');
	block_<?= $item['name']; ?>.nextElementSibling.remove();
	block_<?= $item['name']; ?>.remove();
};
<?php } ?>
<?php if ($item['delay']) { ?>
window.onload = function(e){
	setTimeout(function(){
		let block_<?= $item['name']; ?> = document.getElementById('block-<?= $item['name']; ?>');
		block_<?= $item['name']; ?>.style.display = 'block';
	}, <?= ((float) $item['delay']) * 1000; ?>);
};
<?php } ?>
</script>