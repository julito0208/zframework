<style type="text/css">

body {
	background: #CCC;
}

div.body {

}

div.box {
	border: solid 1px #777;
	margin: 40px 20px;
	padding: 0px 20px 20px 20px;
	background: #F1F1F1;
}

div.box-content {
	padding: 10px 0 0 0;

}


span.checkbox input {
	vertical-align: middle;

}

span.checkbox label {
	vertical-align: middle;
	font-weight: bold;
	cursor: pointer;
}

span.checkbox label:hover {
	text-decoration: underline;
}

div.box h4.box-title {
	display: inline;
	border: solid 1px #000;
	position: relative;
	top: -10px;
	left: -10px;
	background: #444;
	color: #EEE;
	padding: 2px 8px;
}


div.box table.tables {
	width: 100%;
}


div.box table.tables td {
	padding: 0px 5px;
}

div.box table.tables td input {
	vertical-align: middle;
}

div.box table.tables td label {
	vertical-align: middle;
	font-size: 9pt;
	cursor: pointer;
	color: #333;
}

div.box table.tables td label:hover {
	text-decoration: underline;
}


div.box table.tables td.selected label {
	color: #000;

}

.small {
	font-size: 9pt;
}

div.separator {

	border-bottom: solid 1px #CCC;
	margin: 20px 0 20px;

}

span.text-input {

}

span.text-input label {

	margin-right: 10px;
	font-weight: bold;

}

span.text-input input {
	width: 500px;
	font-size: 9pt;
	padding: 2px;
	border: solid 1px #333;
}

input[type="radio"], input[type="checkbox"]
{
	margin-top: 0;
}
span.checkbox input,
span.radio input
{
	margin: 5px 0 0 0 !important;
	vartical-align: top !important;

}

span.checkbox input
{
	margin-top: 7px !important;
}


span.checkbox label,
span.radio label
{
	margin: 0 !important;
	vartical-align: top !important;
	font-weight: bold;
}

span.checkbox label:hover,
span.radio label:hover
{
	text-decoration: underline;
}

</style>

<?php if($submit && $method == HTMLPageDevelopToolEntityGenerator::METHOD_DELETE) { ?>

<form id="formDelete" method="get" action="">

	<?php foreach(array_values($selected_tables) as $index => $table) { ?>
<!--			<input type="hidden" name="tables[]" value="--><?//=$table?><!--" />-->
	<?php } ?>
		
<!--		<input type="hidden" name="method" value="--><?//=HTMLPageDevelopToolEntityGenerator::METHOD_DELETE?><!--" />-->


	<div class="body">

		<div class="box">

			<h4 class="box-title">Tables Removed</h4>

			<table class="tables">

				<?php foreach(array_values($selected_tables) as $index => $table) { ?>

					<tr>
						<td style="font-weight: bold; padding: 5px 0 2px 0">
							<?=$table?>
						</td>
					</tr>

				<?php } ?>

			</table>	

		</div>

	</div>	

	<div class="box" style="padding-top: 20px; text-align: right">
		<button style="cursor: pointer; padding: 4px 10px; border: solid 1px #555; font-weight: bold; background: #EEE;" type="submit">Volver</button>
	</div>	
	
</form>


<?php } else if($submit && $method == HTMLPageDevelopToolEntityGenerator::METHOD_GENERATE) { ?>

<form id="formGenerate" method="get" action="">

	<?php foreach(array_values($selected_tables) as $index => $table) { ?>
<!--			<input type="hidden" name="tables[]" value="--><?//=$table?><!--" />-->
	<?php } ?>

	<?php foreach($generate_data as $key => $value) { ?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
	<?php } ?>
		
	<input type="hidden" name="method" value="<?=HTMLPageDevelopToolEntityGenerator::METHOD_GENERATE?>" />

	<div class="body">

		<div class="box">

			<h4 class="box-title">Tables Entities Generated</h4>

			<table class="tables">

				<?php foreach(array_values($selected_tables) as $index => $table) { ?>

					<tr>
						<td style="font-weight: bold; padding: 5px 0 2px 0">
							<?=$table?>
						</td>
					</tr>

				<?php } ?>

			</table>	

		</div>
		
		
		<?php if($generate_data['generate_entities'] || $generate_data['generate_entities_database']) { ?>

			<div class="box">

				<h4 class="box-title">Entities</h4>

				<table class="tables">

					<?php if($generate_data['generate_entities']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Entities
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_entities_database']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Entities Database
							</td>
						</tr>

					<?php } ?>

				</table>	

			</div>
		
		<?php } ?>
		
		
		<?php if($generate_data['generate_services'] || $generate_data['generate_services_cache'] || $generate_data['generate_services_database'] || $generate_data['generate_services_get'] || $generate_data['generate_services_list'] || $generate_data['generate_services_save'] || $generate_data['generate_services_delete']) { ?>

			<div class="box" style="">

				<h4 class="box-title">Services</h4>

				<table class="tables">

					<?php if($generate_data['generate_services']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_cache']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services Cache
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_database']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services Database
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_get']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services Get
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_list']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services List
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_save']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services Save
							</td>
						</tr>

					<?php } ?>
						
					<?php if($generate_data['generate_services_delete']) { ?>

						<tr>
							<td style="font-weight: bold; padding: 5px 0 2px 0">
								Generate Services Delete
							</td>
						</tr>

					<?php } ?>
						
				</table>	

			</div>
		
		<?php } ?>

					
		<?php if($generate_data['overwrite']) { ?>

			<div class="box">

				<h4 class="box-title">Overwrite</h4>

				<table class="tables">

					<tr>
						<td style="font-weight: bold; padding: 5px 0 2px 0">
							Overwrite
						</td>
					</tr>

				</table>	

			</div>
		
		<?php } ?>

	</div>	

	<div class="box" style="padding-top: 20px; text-align: right">
		<button style="cursor: pointer; padding: 4px 10px; border: solid 1px #555; font-weight: bold; background: #EEE;" type="submit">Volver</button>
	</div>	
	
</form>

<?php } else if(empty($tables)) { ?>

	<br />
	<h4>No existen tablas</h4>

<?php } else { ?>

<form id="generator-form" method="post" action="" onsubmit="return formSubmit()">

	<input type="hidden" name="submit" value="1" />

	<div class="body">

		<div class="box">

			<h4 class="box-title">Tables</h4>

			<table class="tables" style="">

				<tr>
				<?php foreach(array_values($tables) as $index => $table) { ?>

					<?php if($index % $tables_columns == 0) echo "</tr><tr>"; ?>

						<td <?php if(in_array($table, $selected_tables)) echo " class='selected'"; ?> style="width: 0; white-space: nowrap; padding: 8px 0;<?=($index % $tables_columns == 0 ? '' : 'padding-left: 30px;')?>">
							<input class="table-checkbox" type="checkbox" name="tables[]" id="table_<?php echo $table; ?>" value="<?php echo $table; ?>" onclick="if($(this).attr('checked')) { $(this).parent().addClass('selected'); $('#block_name_<?=$table?> input').attr('disabled', false); } else { $(this).parent().removeClass('selected'); $('#block_name_<?=$table?> input').attr('disabled', true); } " <?php if(in_array($table, $selected_tables)) echo " checked='checked'"; ?> />
							<label style="font-weight: bold; font-size: 10pt; padding-left: 5px;" for="table_<?php echo $table; ?>"><?php echo $table; ?></label>
						</td>
						<td>
							<div style="padding: 8px 0 8px 15px;<?=(array_key_exists($table, $tables_classnames) ? '' : 'visibility: hidden;')?>" class="" id="block_name_<?=$table?>">
								<input class="classname-input" type="text" name="classnames[<?=$table?>]" id="name_<?=$table?>" style="border: solid 1px #444;" value="<?=(array_key_exists($table, $tables_classnames) ? HTMLHelper::escape($tables_classnames[$table]) : '')?>" <?php if(!in_array($table, $selected_tables)) echo " disabled='disabled'"; ?> />
							</div>
						</td>


				<?php } ?>

				</tr>
			</table>

			
			<div class='separator'></div>

			<div style='text-align: right'>

				<a class="small" href='javascript: void(0)' onclick="$('table.tables').find('input').each(function() { if(!$(this).attr('checked')) $(this).click().triggerHandler('click'); })">Seleccionar Todos</a> &nbsp; &nbsp;
				<a class="small" href='javascript: void(0)' onclick="$('table.tables').find('input').each(function() { if($(this).attr('checked')) $(this).click().triggerHandler('click'); })">Seleccionar Ninguno</a> &nbsp; &nbsp;
				<a class="small" href='javascript: void(0)' onclick="$('table.tables').find('input').each(function() { $(this).click().triggerHandler('click'); })">Invertir</a> 

			</div>	

		</div>

		<div class="box">

			<h4 class="box-title">Method</h4>

			<div class="box-content">

				<span class="radio">

					<input type="radio" id="method-generate" name="method" value="<?=HTMLPageDevelopToolEntityGenerator::METHOD_GENERATE?>"  <?php if(!$method || $method == HTMLPageDevelopToolEntityGenerator::METHOD_GENERATE) echo " checked='checked'"; ?> />
					<label for="method-generate">Generate Entities & Services</label>

				</span>	


				<span class="radio">

					<input type="radio" id="method-delete" name="method" value="<?=HTMLPageDevelopToolEntityGenerator::METHOD_DELETE?>"  <?php if($method == HTMLPageDevelopToolEntityGenerator::METHOD_DELETE) echo " checked='checked'"; ?> />
					<label for="method-delete">Delete Tables</label>

				</span>	

			</div>	



		</div>

		<div id="generate-container" <?php if($method == HTMLPageDevelopToolEntityGenerator::METHOD_DELETE) echo " style='display: none'"; ?>>

			<div class="box" style="display: none">

				<h4 class="box-title">Services</h4>

				<div class="box-content">

					<div>

						<span class="checkbox">

							<input type="checkbox" id="gen-services" name="generate_services" value="1"  <?php if($generate_data['generate_services']) echo " checked='checked'"; ?> />
							<label for="gen-services">Generate Services</label>

						</span>	

					</div>	

					<div>

						<span class="checkbox">

							<input type="checkbox" id="gen-services-cache" name="generate_services_cache" value="1"  <?php if($generate_data['generate_services_cache']) echo " checked='checked'"; ?> />
							<label for="gen-services-cache">Generate Services Cache</label>

						</span>	

					</div>	

					<div>

						<span class="checkbox">

							<input type="checkbox" id="gen-services-db" name="generate_services_database" value="1"  <?php if($generate_data['generate_entities_database']) echo " checked='checked'"; ?> />
							<label for="gen-services-db">Generate Services Database</label>

						</span>	

					</div>	


					<div id="services-options">

						<br />

						<div>

							<span class="checkbox">

								<input type="checkbox" id="gen-services-get" name="generate_services_get" value="1"  <?php if($generate_data['generate_services_get']) echo " checked='checked'"; ?> />
								<label for="gen-services-get">Generate Get Methods</label>

							</span>	

						</div>	

						<div>

							<span class="checkbox">

								<input type="checkbox" id="gen-services-list" name="generate_services_list" value="1"  <?php if($generate_data['generate_services_list']) echo " checked='checked'"; ?> />
								<label for="gen-services-list">Generate List Methods</label>

							</span>	

						</div>	

						<div>

							<span class="checkbox">

								<input type="checkbox" id="gen-services-save" name="generate_services_save" value="1"  <?php if($generate_data['generate_services_save']) echo " checked='checked'"; ?> />
								<label for="gen-services-save">Generate Save Method</label>

							</span>	

						</div>	

						<div>

							<span class="checkbox">

								<input type="checkbox" id="gen-services-delete" name="generate_services_delete" value="1"  <?php if($generate_data['generate_services_delete']) echo " checked='checked'"; ?> />
								<label for="gen-services-delete">Generate Delete Method</label>

							</span>	

						</div>	

					</div>	


				</div>	

			</div>


			<div class="box">

				<h4 class="box-title">Options</h4>

				<div class="box-content">

					<span class="checkbox">

						<input type="checkbox" id="overwrite" name="overwrite" value="1"  <?php if($generate_data['overwrite']) echo " checked='checked'"; ?> />
						<label for="overwrite">Overwrite</label>

					</span>	

				</div>	



			</div>


		</div>	

	</div>	

	<div class="box" style="padding-top: 20px; text-align: right">

		<button style="cursor: pointer; padding: 4px 10px; border: solid 1px #555; font-weight: bold; background: #EEE;" type="submit">Aceptar</button>

	</div>	

</form>	

<script type="text/javascript">

	$(document).ready(function() {
		$('#gen-entities').triggerHandler('click');
		$('#gen-services').triggerHandler('click');
	});


	$('#method-delete').bind('click', function() {
		$('#generate-container').hide();
		$('.classname-input').attr('disabled', true).css('visibility', 'hidden');
	})

	$('#method-generate').bind('click', function() {
		$('#generate-container').show();
		$('.classname-input').attr('disabled', false).css('visibility', '');
		$('.table-checkbox').trigger('click');
		$('.table-checkbox').trigger('click');
		$('.classname-input').each(function(index, node){
			$(this).attr('disabled', !$(this).attr('disabled'));
		});
	})


	function formSubmit() {

		if($('table.tables').find('input').filter(':checked').length == 0) {

			alert('Debe elegir una tabla por lo menos');
			return false;

		} else {

			if($('#method-generate').attr('checked')) {

				if(!$('#gen-entities').attr('checked') && !$('#gen-services').attr('checked') && !$('#gen-services-db').attr('checked') && !$('#gen-entities-db').attr('checked')) {

					alert('Debe elegir para generar entidades o servicios');
					return false;

				} else if($('#overwrite').attr('checked') && !confirm('Esta por sobreescribir archivos existenes.\nEsta seguro?')) {

					return false;

				} 

				return true;

			} else {

				if(confirm('�Est� seguro que desea eliminar estas tablas?')) {

					return true;

				} else {

					return false;
				}

			}

		}	
	}
</script>	

<?php } ?>	

