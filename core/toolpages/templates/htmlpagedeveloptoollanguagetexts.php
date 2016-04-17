<style type="text/css">

	#css-files-block,
	#js-files-block {
		display: inline-block;
		width: 48%;
	}
	
	#css-files-block {
		float: right;
	}
	
	.list-block {
		vertical-align: top;
		border: solid 1px #000;
		background: #FFF;
		padding: 10px 10px 30px;
		margin: 40px 0 0 0;
	}
	
	.list-block .buttons {
		text-align: right;
		margin: 30px 10px 0 0;
		clear: both;
	}
	
	.list-block .buttons a.button.first {
		margin: 0 20px 0 0;
	}
	
	.list-block .title{
		font-weight: bold;
		margin: 0 0 20px 10px;
		text-decoration: underline;
	}
	
	.list-main {
		vertical-align: top;
		display: block;
	}

	.list-main li.dirname {
		border-bottom: solid 1px #BBB;
		margin: 0 10px 0 20px;
	}
	
	.list-main li.dirname li.dir-contents {
		list-style-type:none;
	}
	
	
	.list-main li.dir-contents ul {
		padding-left: 2%;
	}
	
	.list-main li.dirname.first-dirname {
		border-top: solid 1px #CCC;
	}

	.list-main li.file {
		margin: 5px 10px 10px 7%;
		padding: 0 0 10px 0;
		border-bottom: solid 1px #EEE;
	}

	.list-main li.file.first {
		padding-top: 15px;
	}

	.list-main li.file.last {
		margin-bottom: 15px;
	}

	.list-main li.file input{
		vertical-align: middle;
		margin: 0 10px 0 0;
	}

	.list-main li.file label{
		vertical-align: middle;
		color: #000;
	}
	
	.list-main li.file label:hover {
		text-decoration: underline;
		cursor: pointer;
	}

	.list-main li.dirname a{
		text-decoration: none;
		display: block;
		padding: 10px 0 10px 20px;
	}
	
	.list-main li.dirname a:hover{
		background: #EEE;
	}

	.list-main li.dirname .sign{
		margin: 0 5px 0 0;
	}

	.list-main li.dirname .name {
		text-decoration: underline;
		font-weight: bold;
	}

	.form-top {
		padding-bottom: 20px; 
		border-bottom: solid 1px #777; 
		margin: 0px 0 10px 0;
	}
	
	.form-top .form-title {
		float: left;
	}
	
	
	.form-buttons {
		text-align: right; 
	}

	.form-buttons.bottom {
		padding-top: 20px; 
		border-top: solid 1px #777; 
		margin: 50px 0 0 0;
	}

	.form-buttons button {
		cursor: pointer; 
		padding: 6px 30px; 
		border: solid 1px #555; 
		font-weight: bold; 
		background: #EEE;
		box-shadow: 1px 1px 1px 1px rgba(0,0,0,0.2);
		border-radius: 4px;
		font-size: 10pt;
	}

	.select-all-buttons {
		display: inline;
		margin: 0 30px 0 0;
	}
	
	.select-all-buttons .button.first {
		margin: 0 20px 0 0;
	}
	
	.success-msg,
	.delete-msg {
		font-size: 13pt;
		font-weight: bold;
		margin: 30px 0 10px 0;
		border-bottom: solid 1px #555;
		padding: 0 0 30px 0;
	}
		
	.success-msg {
		color: #00A;
	}
	
	.delete-msg {
		color: #F00;
	}
	
	.mimify-button {
		margin: 0 20px 0 0;
	}

	.input-texts-container {
		
	}

	.input-texts-container-content {
		background: #DEDEDE;
		padding: 20px 0 20px;
		border-radius: 10px;
	}

	.input-text-container {
		padding: 10px 0 10px !important;
		margin: 0 20px;
	}
	
	.input-text-container label {
		display: inline-block;
		vertical-align: middle;
		padding: 0 20px 5px 0px;
		font-weight: bold;
		float: left;
	}
	
	.input-text-container label span.language {
		margin: 0 0 0 10px;
		font-weight: normal;
	}
	
	.input-text-container * {
		vertical-align: middle;
	}
	
	.input-text-container textarea {
		border: solid 1px #666;
		padding: 4px;
		display: inline-block;
		height: 100px;
	}
	
	.section-controls a {
		font-size: 10pt !important;
		text-decoration: none;
		font-weight: normal !important;
	}
	
	.section-controls a:hover {
		text-decoration: underline !important;
	}
	
	.help-text {
		font-weight: bold;
		margin: 10px 0 30px;
	}
	
	.input-text-container label
	{
		width: auto;
		display: block;
		
	}
	
	.input-text-container textarea
	{
		width: auto;
		display: block;
		width: 100%;
	}
	
</style>

<form id="languages-form" method="post" action="" style="margin: 30px 0 0 0;">
	
	<div>
	
		<div class="help-text" style="float:left">
			Mientras escriba un texto, presione CTRL+Enter para hacer aplicar los cambios<br />
			Dentro de una sección, puede presionar CTRL+Espacio para agregar un texto rápidamente
		</div>
		
		<div class="align-right" style="float:right">
			
			<div class="box form-buttons bottom" style="border: 0; padding: 0; margin: 0 0 0 40px; display: inline-block; ">
				<button class="delete-button" type="button" style="background: #F2F2F2; color: #222; " onclick="addSection()">Add Section</button>
			</div>	
			
		</div>
		
		<div style="clear:both"></div>
	</div>
	
	<br />
	
	<? foreach($sections_data as $section_data) { ?>

		<div style="background: #F2F2F2; box-shadow: 1px 1px 1px rgba(0,0,0,0.4); border: solid 1px #999; margin: 0 0 40px 0; padding: 20px; " class="section-row" id="section-row-<?=$section_data['section']->get_id_language_section()?>">
	
			<a style="font-weight: bold; font-size: 13pt; text-decoration: none; display: block" href="javascript:void(0)" onclick="toggleSection(this)">
				<span class="sign" style="display: inline-block; width: 15px;"><?=($section_data['section']->get_id_language_section() == $selected_section ? '-' : '+')?></span>
				<span class="text" style="text-decoration:underline;"><?=$section_data['section']->get_id_language_section()?></span>
			</a>

			<div class="section-controls pull-right align-right" style="padding: 0 20px 0 0;<?=($section_data['section']->get_id_language_section() == $selected_section ? '' : ' display:none;')?>">
				
				<a style="font-weight: bold; font-size: 13pt; text-decoration: none; " href="javascript:void(0)" onclick="<?=HTMLHelper::escape(JSHelper::call_quote('addText', $section_data['section']->get_id_language_section()))?>" class="add-text-link">
					Add Text
				</a>
				&nbsp;&nbsp;&nbsp;
				<a style="font-weight: bold; font-size: 13pt; text-decoration: none; " href="javascript:void(0)" onclick="<?=HTMLHelper::escape(JSHelper::call_quote('deleteSection', $section_data['section']->get_id_language_section()))?>">
					Delete Section
				</a>

			</div>

			<br />
			
			<? if(!empty($section_data['texts_languages'])) { ?>
			
				<div class="section-container" style="margin: 0px 0px 30px;<?=($section_data['section']->get_id_language_section() == $selected_section ? '' : ' display:none;')?>">

					<br />

					<? foreach($section_data['texts_languages'] as $id_language_text => $texts_array) { ?>
					
						<div class="input-texts-container">
						
							<div class="<?=HTMLHelper::escape('input-texts-container-content input-texts-'.$id_language_text)?>" style="box-shadow: 1px 1px 1px rgba(0,0,0,0.4); border: solid 1px #777;">

								<? foreach($texts_array['texts'] as $id_language => $text_data) { ?>

									<div style="padding: 0px 0 0px 20px; " class="input-text-container">
										<?=$text_data['input']?>
									</div>

								<? } ?>
								
								<div style="padding: 20px 0 0px 40px">
									<span class="input-checkbox checkbox">
										<input type="checkbox" name="texts_javascripts[<?=$section_data['section']->get_id_language_section()?>][<?=$id_language_text?>]" id="text-javascript-<?=$section_data['section']->get_id_language_section()?>-<?=$id_language_text?>" value="1" <?=($texts_array['javascript'] ? " checked='checked'" : '')?> />
										<label for="text-javascript-<?=$section_data['section']->get_id_language_section()?>-<?=$id_language_text?>">Activar Javascript</label>
									</span>
								</div>
								
							</div>
							
						</div>
					
					
						<div class="controls">
						
							<div style="padding: 15px 5px 30px 10px; float:left" class="align-left">
								<a href="javascript:void(0)" onclick="<?=HTMLHelper::escape(JSHelper::call_quote('updateText', $id_language_text, $section_data['section']->get_id_language_section()))?>">Update Text</a>
							</div>

							<div style="padding: 15px 10px 30px 0px; float:right" class="align-right">
								<a href="javascript:void(0)" onclick="<?=HTMLHelper::escape(JSHelper::call_quote('deleteText', $id_language_text, $section_data['section']->get_id_language_section()))?>">Delete Text</a>
							</div>
							
						</div>
					
						<div class="clear-both"></div>

						<br />
					
					<? } ?>

				</div>
			
			<? } ?>

		</div>
	
	<? } ?>
	
	<br />
	
	<div class="box form-buttons bottom">
		<button id="texts-submit-button" class="delete-button" type="submit" style="background: #333; color: #FFF; ">Submit</button>
	</div>	
	
	<input type="hidden" name="submit" value="1" />
	<input type="hidden" name="section" value="<?=HTMLHelper::escape($selected_section)?>" />
	<input type="hidden" name="language_text" value="<?=HTMLHelper::escape($selected_language_text)?>" />

	<br />
	<br />
	
</form>

<script type="text/javascript">
		
	function addSection() {
		
		$.modalDialog.prompt('Section', '', function(value) {
			
			setTimeout(function() {
			
				$.modalDialog.loading('Loading...', function() {
				
					$.ajax({
						'url': '!HTMLPageDevelopToolLanguageTexts(add_section)',
						'type': 'post',
						'data': {'section': value, 'href': location.href},
						'success': function(data) {

							if(data && data['error']) {

								$.modalDialog.alert(data['error'], {'mode': $.modalDialog.modeReplaceAll, 'onclose': function () { Navigation.reload(); } });

							} else if(data && data['success']) {

								Navigation.go(data['url']);
							}

						}

					});

				}, {'mode': $.modalDialog.modeReplaceAll});

			}, 500);
			
		}, 'Add Section');
	}	
		
	function toggleSection(node) {
		
		node = $(node);
			
		var parent = node.parents('.section-row');
		var sectionHtml = parent.find('.section-container');
		var sectionControlsHtml = parent.find('.section-controls');
		
		
		if(sectionHtml.is(':visible')) {
			
			node.find('.sign').html('+');
			sectionHtml.css({'display': 'none'});
			sectionControlsHtml.css({'display': 'none'});
			
		} else {
			
			node.find('.sign').html('-');
			sectionHtml.css({'display': 'block'});
			sectionControlsHtml.css({'display': 'block'});
			sectionHtml.find('textarea').attr({'disabled': false});
			
		}
		
		$(window).triggerHandler('resize');
		
		
	}	
		
	function setLanguage(idLanguage) {
		Navigation.reload({'language': idLanguage});
	}	
	
		
	function deleteSection(idSection) {
		
		$.modalDialog.confirm('Are you sure to delete this section?', function() {
			
			$.modalDialog.loading('Loading...', function() {
				
				$.ajax({
					'url': '!HTMLPageDevelopToolLanguageTexts(delete_section)',
					'type': 'post',
					'data': {'section': idSection},
					'success': function(data) {
						
						if(data && data['error']) {
							
							$.modalDialog.alert(data['error'], {'mode': $.modalDialog.modeReplaceAll, 'onclose': function () { Navigation.reload(); } });
							
						} else if(data && data['success']) {
							
							Navigation.reload();
						}
						
						
					}
					
				});
				
			}, {'mode': $.modalDialog.modeReplaceAll});
		});
	}
	
	function addText(idSection) {
		
		$.modalDialog.prompt('Text', '', function(value) {

			setTimeout(function() {

				$.modalDialog.loading('Loading...', function() {
				
					$.ajax({
						'url': '!HTMLPageDevelopToolLanguageTexts(add_text)',
						'type': 'post',
						'data': {'text': value, 'section': idSection, 'href': location.href},
						'success': function(data) {

							if(data && data['error']) {

								$.modalDialog.alert(data['error'], {'mode': $.modalDialog.modeReplaceAll, 'onclose': function () { Navigation.reload(); } });

							} else if(data && data['success']) {

								Navigation.go(data['url']);
							}

						}

					});

				}, {'mode': $.modalDialog.modeReplaceAll});
			
			}, 500);
			
		}, 'Add Text [' + String(idSection) + ']');
	}
	
	function deleteText(idText, idSection) {
		
		$.modalDialog.confirm('Are you sure to delete this text?', function() {
			
			$.modalDialog.loading('Loading...', function() {
				
				$.ajax({
					'url': '!HTMLPageDevelopToolLanguageTexts(delete_text)',
					'type': 'post',
					'data': {'section': idSection, 'text': idText},
					'success': function(data) {
						
						if(data && data['error']) {
							
							$.modalDialog.alert(data['error'], {'mode': $.modalDialog.modeReplaceAll, 'onclose': function () { Navigation.reload(); } });
							
						} else if(data && data['success']) {
							
							Navigation.reload();
						}
						
						
					}
					
				});
				
			}, {'mode': $.modalDialog.modeReplaceAll});
		});
		
	}
	
	function setSelectedSectionLanguageText(idLanguageText, idSection) {
		
		$('#languages-form input[name=section]').val(idSection);
		$('#languages-form input[name=language_text]').val(idLanguageText);
	}
	
	function updateText(idLanguageText, idSection)
	{
		setSelectedSectionLanguageText(idLanguageText, idSection);
		$('#texts-submit-button').click();
	}
	
	$('.language-text-input').bind('keypress', function(evt) {
		
		if(evt.which == $.KEY_ENTER && evt.ctrlKey) {
			
			var idSection = $(this).attr('id-section');
			var idLanguageText = $(this).attr('id-language-text');
			
			setSelectedSectionLanguageText(idLanguageText, idSection);
			
			$('#texts-submit-button').click();
		}
		
	});
	
	$('.section-row').bind('keypress', function(evt) {
		
		if(evt.which == $.KEY_SPACE && evt.ctrlKey) {
			
			$(this).find('.add-text-link').click();
			
		}
		
	});
	
	$('.language-text-input').bind('focus', function(evt) {
		
		setSelectedSectionLanguageText($(this).attr('id-section'), $(this).attr('id-language-text'));
		
	});
	
	<? if($selected_section) { ?>
		
		try {
		
			$('#section-row-<?=$selected_section?>').scrollWindowTop();

			<? if($selected_language_text) { ?>
				$('#section-row-<?=$selected_section?> .input-texts-<?=$selected_language_text?>').scrollWindowTop();

				$(document).ready(function() {
					$('*[id-language-text=<?=$selected_language_text?>][id-section=<?=$selected_section?>]').eq(0).focus();
				});

			<? } ?>
				
		} catch(e) {}
		
	<? } ?>
		
</script>