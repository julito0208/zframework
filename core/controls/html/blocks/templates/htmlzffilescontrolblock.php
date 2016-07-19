
<div style="display: inline-block" class="files-container">

	<?php if($enable_delete) { ?>
		<input class="files-delete-input" type="hidden" name="<?=HTMLZFFilesControlBlock::REQUEST_DELETE_VARNAME?>" id="<?=$id?>_delete" value="" />
	<?php } ?>

	<?php if($enable_input) { ?>
		<div class="input-container" style="<?=(!empty($files) ? "margin: 0 0 20px 0;" : '')?>">
			<?=$input?>
		</div>
	<?php } ?>

	<?php if($files && !empty($files)) { ?>

		<ul class="files">

			<?php foreach($files as $file) { ?>

				<li style="padding: 2px 0">
					<a href='<?=NavigationHelper::conv_abs_url($file->get_path())?>' title='<?=HTMLHelper::escape($file->title)?>' target='_blank' class="icon-link">
						<span class="icon <?=ZFFile::get_type_icon($file->get_mimetype())?>"></span>
						<span class="text"><?=HTMLHelper::escape($file->title)?></span>
					</a>
					<?php if($enable_delete) { ?>
						&nbsp;
						<a href="javascript:void(0)" title="Eliminar" class="text text-danger" onclick="$(this).getParent('.files-container').find('.files-delete-input').val($(this).getParent('.files-container').find('.files-delete-input').val().split(',').append('<?=$file->id_file?>').join(',').trim(',')); $(this).getParent('li').remove();">
							<span class="icon fa fa-remove"></span>
						</a>
					<?php } ?>
				</li>

			<?php } ?>

		</ul>

	<?php } ?>

</div>