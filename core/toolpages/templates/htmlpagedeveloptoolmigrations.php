<style type="text/css">

	table
	{
		border-collapse: collapse;
		width: 100%;
		border: solid 1px #333;
		margin: 30px 0 0;
	}

	table caption
	{
		font-weight: bold;
		font-size: 14pt;
		color: #222;
		padding: 0 0 20px 0;
	}

	table td,
	table th
	{
		border: solid 1px #333;
		padding: 5px 20px;
	}

	table th
	{
		background: #555;
		color: #FFF;
		padding: 10px 20px;
	}

	table td
	{
		background: #F2F2F2;
		color: #333;
		padding: 10px 20px;
		cursor: pointer;
	}

	table td input
	{
		cursor: pointer;
	}

	table tr:hover td
	{
		background: #DDD !important;
	}

	table td.col-radio
	{
		width: 50px;
		text-align: center;
	}

	table td.col-estado
	{
		font-weight: bold;
	}

	table tr.processed td
	{
		opacity: 0.4;
	}

	table tr.selected td
	{
		background: #888 !important;
		color: #FFF !important;
		text-decoration: underline;
	}

	table tr.selected td .text
	{
		color: #FFF !important;
	}

</style>

<br />
<br />

<button type="button" style="padding: 5px 30px;" class="btn btn-success" onclick="addMigration()"><span class="fa fa-plus"></span> &nbsp; Agregar SQL</button>

<br />
<br />

<?php if($migrations_success): ?>
	<br />
	<div class="alert alert-info strong">
		Las migraciones fueron corridas
	</div>
<?php endif; ?>

<?php if(!empty($migrations)) { ?>

	<form method="post" action="" id="migration-form" onsubmit="return submitMigrationForm()">

		<table class="migrations-table">

			<caption>Migraciones</caption>

			<thead><tr>
				<th class="col-radio">&nbsp;</th>
				<th class="col-estado">Estado</th>
				<th class="col-nombre">Nombre</th>
			</tr></thead>

			<tbody>

				<?php foreach ($migrations as $migration): ?>

					<tr class="<?=(in_array($migration, $processed_migrations) ? 'processed' : 'pending')?> <?=($selected_id_migration == $migration ? 'selected' : '')?>">
						<td class="col-radio">
							<input type="radio" name="id_migration" value="<?=HTMLHelper::escape($migration)?>" <?=($selected_id_migration == $migration ? 'checked="checked"' : '')?> />
						</td>
						<td class="col-estado">
							<?php if(in_array($migration, $processed_migrations)): ?>
								<span class="text text-success">Procesado</span>
							<?php else: ?>
								<span class="text text-info">Pendiente</span>
							<?php endif; ?>
						</td>
						<td class="col-nombre">
							<?php echo $migration; ?>
						</td>

					</tr>

				<?php endforeach; ?>

			</tbody>


		</table>
		<br />
		<br />

		<div style="padding: 0px 0 0 30px">
			<div style="text-align: left; float: left">

				<span class="radio">

					<input type="radio" name="id_migration" value="-1" id="migration_reset" />
					<label style="padding: 0; font-weight: bold; color: #F00; " class="text text-danger hover-underline" for="migration_reset">Deshacer todas las migraciones</label>

				</span>

			</div>

			<div style="text-align: right; float: right">
				<button type="submit" style="font-weight: bold; padding: 5px 30px;" class="btn btn-info">Correr Migraciones</button>
			</div>
		</div>

		<div class="clear"></div>

	</form>

<?php } else { ?>

	<h4>No existen migraciones</h4>

<?php } ?>

<script type="text/javascript">

	$('.migrations-table td').bind('mousedown', function(evt) {

		evt.stopPropagation();

		var cell = $(this);
		var row = cell.getParent('tr');
		var radio = row.find('input');

		$('.migrations-table tr').removeClass('selected');
		row.addClass('selected');

		radio.click();
	});

	$('#migration_reset').bind('click', function() {
		$('.migrations-table tr').removeClass('selected');
	});

	function addMigration()
	{
		var name = prompt('Nombre');
		name = name.trim();

		if(name)
		{
			$.zmodal.loading(function() {

				$.ajax({
					'url': '!HTMLPageDevelopToolMigrations(add_migration)',
					'type': 'post',
					'data': {name: name},
					'success': function(data)
					{
						$.zmodal.closeAll();

						if(data['success'])
						{
							Navigation.reload();
						}
						else
						{
							$.zmodal.alert(data['error'] ? data['error'] : 'Ocurrió un error desconocido');
						}
					}
				});

			});
		}
	}

	function submitMigrationForm()
	{
		var confirmText;

		if($('#migration_reset').is(':checked'))
		{
			confirmText = '¿Está seguro que desea reiniciar todas las migraciones?';
		}
		else
		{
			confirmText = '¿Está seguro que desea ir a esta migracion?';
		}

		if(confirm(confirmText))
		{
			return true;
		}

		return false;

	}

</script>

