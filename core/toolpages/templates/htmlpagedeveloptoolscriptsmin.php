<? //---------------------------------------------------------------

function echo_dir_list($item, $checked_files, $opened_dirs, $first=false) {
	
	if($item['type'] == 'dir') {

		$dir_contents = array();
		$files_contents = array();
			
		foreach($item['contents'] as $content) {
			
			if($content['type'] == 'dir') {
				$dir_contents[] = $content;
			} else {
				$files_contents[] = $content;
			}
		}
		
		if(!$show) {
			
			$show = in_array($item['path'], $opened_dirs);
		}
		
		echo "<li class='dirname dir-hidden".($first ? ' first-dirname' : '')."'><input type='checkbox' style='float:left; position: relative; top: 13px; cursor:pointer;' onclick='toggleDirCheck(this)' /><a href='javascript:void(0)' onclick='toggleDir(this)'><span class='sign'>".($show ? '-' : '+')."</span><span class='name'>".$item['name']."</span></a></li>";
		echo "<li class='dir-contents'>";
		
		if($show) {
			echo "<ul class='' style=''>";
		} else {
			echo "<ul class='dir-hidden' style='display: none'>";
		}
		
		
		foreach($dir_contents as $content) {
			echo_dir_list($content, $checked_files, $opened_dirs);
		}
		
		$count_files = count($files_contents);
		
		foreach($files_contents as $file_index => $content) {
			
			$checked = in_array($content['path'], $checked_files);
		
			echo "<li class='file".($file_index == 0 ? ' first' : '').($file_index == $count_files-1 ? ' last' : '')."'>";
			echo "<input type='checkbox' name='files[".$content['extension']."][]' value='".$content['path']."' id='file-".$content['path']."' ".($checked ? " checked='checked'" : '')."/>";
			echo "<label for='file-".$content['path']."'>".$content['name']."</label>";
			echo "</li>";

		}
		
		echo "</ul>";
		echo "</li>";

	} 
}

//------------------------------------------------------------------ ?>

<style type="text/css">

	body {
		background: #CCC;
	}

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
	
	.list-main li.dirname {
		padding-left: 10px;
	}
	
	.list-main li.dirname:hover{
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

</style>

<form id="scriptsmin-form" method="post" action="" onsubmit="return false;" style="margin-top: 30px">
	
	<div class="form-top">
		
		<div class="box form-buttons top">
			<div class="buttons select-all-buttons">
				<a class="first button small" href='javascript: void(0)' onclick="selectAll('#js-files-block'); selectAll('#css-files-block');">Seleccionar Todos</a>
				<a class="small button" href='javascript: void(0)' onclick="$('#js-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); }); $('#css-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); });">Seleccionar Ninguno</a> 
			</div>	
			<button class="mimify-button" type="button" onclick="submitMinForm('mimify')">Mimificar archivos</button>
			<button class="delete-button" type="button" onclick="submitMinForm('delete', true)">Eliminar mimificado</button>
		</div>	
	</div>
	
	<? if($success_msg) { ?>
		<div class="success-msg">Se mimificaron los archivos seleccionados</div>
	<? } ?>
	
	<? if($delete_msg) { ?>
		<div class="delete-msg">Se eliminaron los archivos seleccionados</div>
	<? } ?>
	
	<div style="clear: both"></div>
	
	<div id="js-files-block" class="list-block">
		<div class="title">JS Files</div>
		<ul id="js-files-list" class="list-main">
		<? foreach($js_files as $index => $js_file) { ?>
			<?php echo_dir_list($js_file, $selected_js_files, $opened_js_dirs, $index == 0); ?>
		<? } ?>
		</ul>

		<div class="buttons">
			<a class="first button small" href='javascript: void(0)' onclick="selectAll('#js-files-block')">Seleccionar Todos</a>
			<a class="small button" href='javascript: void(0)' onclick="$('#js-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); })">Seleccionar Ninguno</a> 
		</div>	

	</div>

	<div id="css-files-block" class="list-block">
		<div class="title">CSS Files</div>
		<ul id="css-files-list" class="list-main">
		<? foreach($css_files as $index => $css_file) { ?>
			<?php echo_dir_list($css_file, $selected_css_files, $opened_css_dirs, $index == 0); ?>
		<? } ?>
		</ul>

		<div class="buttons">
			<a class="small button first" href='javascript: void(0)' onclick="selectAll('#css-files-block')">Seleccionar Todos</a>
			<a class="small button" href='javascript: void(0)' onclick="$('#js-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); })">Seleccionar Ninguno</a> 
		</div>	

	</div>
	
	<div style="clear: both"></div>
	
	<div class="box form-buttons bottom">
		<div class="buttons select-all-buttons">
			<a class="first button small" href='javascript: void(0)' onclick="selectAll('#js-files-block'); selectAll('#css-files-block');">Seleccionar Todos</a>
			<a class="small button" href='javascript: void(0)' onclick="$('#js-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); }); $('#css-files-block').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); });">Seleccionar Ninguno</a> 
		</div>	
		<button class="mimify-button" type="button" onclick="submitMinForm('mimify')">Mimificar archivos</button>
		<button class="delete-button" type="button" onclick="submitMinForm('delete', true)">Eliminar mimificado</button>
	</div>	

</form>

<script type="text/javascript">
		
	function toggleDir(node) {
		
		node = $(node);
		
		var nextLi = node.parent().next();
		
		var list = nextLi.children('ul');
		
		if(list.hasClass('dir-hidden')) {
			
			list.removeClass('dir-hidden')
			list.slideDown('fast');
			node.find('.sign').html('-');
			
		} else {
			
			list.addClass('dir-hidden')
			list.slideUp('fast');
			node.find('.sign').html('+');
		}
	}	
	
	function selectAll(listBlock) {
		
		listBlock = $(listBlock);
		
		listBlock.find('.dir-hidden').each(function() {
			var $list = $(this);
			$list.removeClass('dir-hidden');
			$list.show();
			$list.find('.sign').html('-');
			$list.find('input').attr('checked', true);
		});
	}
	
	function submitMinForm(action, confirmDialog) {
		
		var form = $('#scriptsmin-form');
		
		var minFiles = [];
		var selectedJSFiles = [];
		var selectedCSSFiles = [];
		
		form.find('#js-files-list input').each(function() {
			var checkBox = $(this);
			if(checkBox.attr('checked')) {
				minFiles.push($(this).val());
				selectedJSFiles.push($(this).val());
			}
		});
		
		form.find('#css-files-list input').each(function() {
			var checkBox = $(this);
			if(checkBox.attr('checked')) {
				minFiles.push($(this).val());
				selectedCSSFiles.push($(this).val());
			}
		});
		

		if(minFiles.length == 0) {
			
			$.modalDialog.alert('No seleccionó ningún archivo', 'error');
			
		} else {
		
			if(confirmDialog) {
				
				$.modalDialog.confirm(action == 'delete' ? '¿Está seguro que desea eliminar los mimificados de estos archivos?' : '¿Está seguro que desea mimificar estos archivos?', function() {
					submitMinForm(action, false);					
				});
			
			} else {

				var fileIndex = 0;

				var loadingHTML = "<div style='width: 270px; text-align: center;'><span class='loading-message'>Procesando archivos <span class='nowrap loading-count-span' style='font-weight: normal; padding: 0 0 0 15px'>( <span class='actual-index' id='call-method-index'>1</span> / <span class='total'>" + minFiles.length + "</span> )</span>... </span></div>";

				var ajaxRequest = function() {

					$('#call-method-index').html(String(fileIndex+1));

					var ajaxParams = {};
					
					ajaxParams.url = Navigation.url;
					
					ajaxParams.type = 'post';
					
					ajaxParams.data = {'file': minFiles[fileIndex], 'action': action};
					
//					ajaxParams.success = function(data) {
//						console.log(data);
//					};
//					
					ajaxParams.complete = function() {

						if(fileIndex < minFiles.length-1) {
							fileIndex = fileIndex + 1;
							ajaxRequest();
						} else {
							var reloadData = {};
							reloadData['selected_action'] = action;
							reloadData['selected_files'] = {};
							reloadData['selected_files']['css'] = selectedCSSFiles;
							reloadData['selected_files']['js'] = selectedJSFiles;
							Navigation.post(Navigation.url, reloadData);
						}

					};

					$.ajax(ajaxParams);
				};

				$.modalDialog.loading(loadingHTML, function() {
					ajaxRequest();
				});
			}
			
		}

		return false;
	}
	
	function toggleDirCheck(check)
	{
		
		var node = $(check);
		var checked = node.attr('checked');
		
		var nextLi = node.parent().next();
		
		var list = nextLi.children('ul');
		list.find('input[type=checkbox]').attr({'checked': checked ? true : false});
		
	}
		
</script>