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
<div class="position-fixed bottom-0 m-2 p-2 bg-white border" id="block-<?= $item['name']; ?>"<?= $item['delay'] ? ' style="display:none;"' : null; ?>>
	<div class="row justify-content-center align-items-center">
		<div class="col-12 col-md col-xl-10 col-xxl-9 pb-2 pb-md-0">
			<?= $item['description'] !== true ? $item['description'] : $view -> get('lang|common:cookie'); ?>
		</div>
		<div class="col-auto">
			<?php if ($item['link']) { ?>
			<a href="<?= $item['link']; ?>" class="btn btn-outline-primary">
				<?= $item['readmore'] !== true ? $item['readmore'] : $view -> get('lang|common:readmore'); ?>
			</a>
			<?php } ?>
			<button type="button" class="btn btn-primary" id="button-<?= $item['name']; ?>">
				<?= $item['agree'] !== true ? $item['agree'] : $view -> get('lang|common:agree'); ?>
			</button>
		</div>
	</div>
</div>
<?php
	if ($item['cookie']) {
		$print .= '
		let button_' . $item['name'] . ' = document.getElementById("button-' . $item['name'] . '"); 
		button_' . $item['name'] . '.onclick = function() {
			' . $item['cookie'] . '
			let block_' . $item['name'] . ' = document.getElementById("block-' . $item['name'] . '");
			block_' . $item['name'] . '.nextElementSibling.remove();
			block_' . $item['name'] . '.remove();
		};
		';
	}
	if ($item['delay']) {
		$print .= '
		window.onload = function(e){
			setTimeout(function(){
				let block_' . $item['name'] . ' = document.getElementById("block-' . $item['name'] . '");
				block_' . $item['name'] . '.style.display = "block";
			}, ' . (((float) $item['delay']) * 1000) . ');
		};
		';
	}
}, null, null, null);

echo $print ? '<script>' . $print . '</script>' : null;

?>