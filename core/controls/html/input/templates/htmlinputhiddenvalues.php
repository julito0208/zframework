<? foreach($values as $name => $item) { ?>
	<?=new HTMLInputHiddenControl("{$item['name']}_id", $item['name'], $item['value'])?>
<? } ?>
