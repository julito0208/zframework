<style type="text/css">

	body {
		background: #CCC;
	}

	div.body {

	}

</style>

<?php eval("\$ztool_title = ".get_class($_this).'::_get_title();'); ?>

<div style='padding: 20px'>

	<div style="font-weight: bold; text-decoration: underline; font-size: 12pt; background: #444; color: #FFF; padding: 10px 20px; border: solid 1px #000;">
		
		<div style="float:left">
			<?php if($ztool_title): ?>
				<span style="font-weight: normal">ZFramework Development Tools</span>
				<span style="padding: 0 7px;">|</span>
				<?=$ztool_title?>
			<?php else: ?>
				ZFramework Development Tools
			<?php endif; ?>
			
		</div>
		
		<?php if($show_back): ?>
			<div style="float:right;"><a href="<?=HTMLHelper::escape($ztools_url)?>" style="color: #FFF; font-size: 10pt; ">&lt; Back</a></div>
		<?php endif;?>
		
		<div style="clear:both"></div>
	</div>
	
	<?=$content?>

</div>

