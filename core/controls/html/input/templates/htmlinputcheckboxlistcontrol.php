<?php //-----------------------------------------------------------------------

$class = trim($class.' checkbox-list');

//----------------------------------------------------------------------- ?>

<div id="<?=HTMLHelper::escape($id)?>" class="<?=HTMLHelper::escape($class)?>">

	<? foreach($inputs as $index => $input) { ?>
		<?=$input?>
	<? } ?>
		
</div>