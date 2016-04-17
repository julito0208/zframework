
<div class="breadcrumb">
	
<? foreach($items as $index => $item) { ?>

	<? if($index > 0) { ?>
		<span class="breadcrumb-separator">&gt;</span>
	<? } ?>
		
	<span class="breadcrumb-item strong">
		<? if($item['href'] && $index < count($items)-1) { ?>
			<a class="breadcrumb-link" href="<?=HTMLHelper::escape($item['href'])?>" title="<?=HTMLHelper::escape($item['text'])?>"><?=HTMLHelper::escape($item['text'])?></a>
		<? } else { ?>
			<?=HTMLHelper::escape($item['text'])?>
		<? } ?>
		
	</span>	

<? } ?>

</div>