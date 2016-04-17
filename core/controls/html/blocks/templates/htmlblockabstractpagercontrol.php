
<? if($pages > 1) { ?>

	<div class="pager block-color">

		<div class="float-left info">
			<span class="actual-page-text"><?=String::get('page')?> <span class='number actual-page'><?=$page_selected+1?></span><span class="separator">/</span><span class='number total-pages'><?=$pages?></span></span>
			<span class="separator main-separator">|</span>
			<span class="count-text"><?=$count_text?></span>
		</div>

		<div class="align-right">

			<? if($first_button) { ?>
				<a class='pager-button' href="<?=HTMLHelper::escape($first_button_url)?>">&lt;&lt;</a>
			<? } ?>

			<? for($page_index=$page_start; $page_index<($page_start+$buttons_count);$page_index++) { ?>

				<? if($page_index == $page_selected) { ?>
					<a class='pager-button selected' href="<?=HTMLHelper::escape($pages_urls[$page_index-$page_start])?>"><?=$page_index+1?></a>
				<? } else { ?>	
					<a class='pager-button' href="<?=HTMLHelper::escape($pages_urls[$page_index-$page_start])?>"><?=$page_index+1?></a>
				<? } ?>

			<? } ?>

			<? if($last_button) { ?>
				<a class='pager-button' href="<?=HTMLHelper::escape($last_button_url)?>">&gt;&gt;</a>
			<? } ?>

		</div>	
	</div>

	<br />
	
<? } else if($pages > 1 && $total_results > 0) { ?>

	<div class="pager block-color">
		<div class="align-right total-count">
			<? if($total_results > 1) { ?>
				<?=String::get('found_n_records', $total_results)?>
			<? } else { ?>
				<?=String::get('found_1_record')?>
			<? } ?>
		</div>		
	</div>	

	<br />
	
<? } ?>