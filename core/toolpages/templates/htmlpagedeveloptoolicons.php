<style type="text/css">
	#tit
	{
		padding: 3px 5px;
		margin: 0 0;
		height: 25px;
		line-height: 25px;
		width: 400px;
		font-size: 11pt;
		border: solid 1px #666;
		background: #FFF;
		border-radius: 4px;

	}

	.no-icon-selected
	{
	}

	.icon-selected
	{
		font-weight: bold;
	}

</style>

<div class="icons-themes" style="margin: 20px 10px 0px 10px; background: #F5F5F5; padding: 10px 20px; border: solid 1px #333;">
	<? foreach($themes as $theme) { ?>
		<a href="<?=$theme['url']?>" title="<?=$theme['theme']?>" style="<?=($selected_theme == $theme['theme'] ? "margin: 0 20px 0 0; color: #888; text-decoration: none" : "margin: 0 20px 0 0")?>"><?=$theme['theme']?></a>
	<? } ?>
</div>

<?php if($show_icon_block_button): ?>

	<div class="align-right" style="padding: 10px 10px 0 0;">

		<?php if($show_icon_block): ?>
			<a href="javascript: void(0)" onclick="Navigation.query({'block': '0'})">Ocultar nombres</a>
		<?php else: ?>
			<a href="javascript: void(0)" onclick="Navigation.query({'block': '1'})">Mostrar nombres</a>
		<?php endif; ?>

	</div>

<?php endif; ?>

<div style="padding: 10px; <?=($selected_theme ? '' : "display: none;")?>">
	
	<div style="background: #EEE; padding: 10px 15px; margin: 20px 0 30px; border: solid 1px #333;">


		<div class="float-left">
			<label style="font-weight: bold; padding: 0 10px 0 0;">Classname</label>
			<input readonly="readonly" class="no-icon-selected" type="text" id="tit" onclick="$(this).select();" onfocus="$(this).select()" placeholder="<?=$unselected_icon_label?>" />
		</div>

		<div class="float-right right-html"><?=$right_title_html?></div>
		
		<div class="clear-both"></div>
		
	</div>
	
	<div style="background: #F5F5F5; padding: 5px 10px; margin: 20px 0 30px; border: solid 1px #333;" onmouseout="showIcon(true)">
		<?=$content?>
	</div>
	
</div>

<script type="text/javascript">
	var selectedIconNode;

	$('.icon-block').css({'padding': '10px 10px', 'display': 'inline-block'});

	function showIcon(node) {

		var title;

		if(!node) return;

		if(node === true) {
			if(!selectedIconNode) title = <?=JSHelper::cast_str($unselected_icon_label)?>;
			else node = selectedIconNode;
		}

		if(node) {
			title = $(node).find('span').attr('class');
		}

		if(!title)
		{
			title = '';
			$('#tit').addClass('no-icon-selected').removeClass('icon-selected');
		}
		else
		{
			$('#tit').removeClass('no-icon-selected').addClass('icon-selected');
		}

		if($('.icon-block').hasClass('icon-block-white'))
		{
			$('.icon-block').css({'background': '#FFF', 'cursor': ''});
		}
		else
		{
			$('.icon-block').css({'background': '', 'cursor': ''});
		}

		$('#tit').val(title);
		$(node).css({'background': '#DDD', 'cursor': 'pointer'});
		showIconTitle = title;
	}
	
	function selectIcon(node) {
		selectedIconNode = node;
		showIcon(node);
	}
</script>
