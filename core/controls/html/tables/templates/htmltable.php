<?php if(empty($rows)): ?>

	<div class="panel panel-default datatable-empty">
		<div class="panel-body">
			<?=$empty_html?>
		</div>
	</div>

<?php else: ?>

	<div id="<?=HTMLHelper::escape('div_container_'.$id)?>" class="datatable-container">
		<table<?=$attr_html?>>
			<thead><tr><?php foreach ($columns as $column): ?><?=$column?><?php endforeach; ?></tr></thead>
			<tbody></tbody>
		</table>
	</div>

	<?php if($reordering) { ?>

		<div class="description">
			Puede reordenar los elementos arrastrándolos
		</div>

	<?php } ?>

	<script type="text/javascript">
		(function() {

			var table = $('#<?=$id?>');

			<?php if($responsive) { ?>
				table.css({'width': '100%'});
			<?php } ?>

			var dataTable = table.dataTable({
				"data": <?=JSHelper::cast_array($prepared_rows)?>,
				"columns": <?=JSHelper::cast_array($prepared_columns)?>,
				"paging": <?=JSHelper::cast_bool(($reordering || count($rows) <= 10) ? false : $paging)?>,
				"info": <?=JSHelper::cast_bool($show_info)?>,
				"ordering": <?=JSHelper::cast_bool($reordering ? false : $ordering)?>,
				"stateSave": <?=JSHelper::cast_bool($stateSave)?>,
				"responsive": <?=($responsive ? 'true' : 'false')?>,
				'searching': <?=JSHelper::cast_bool(!$reordering)?>
			});

			table.data('rowsData', <?=JSHelper::cast_obj($array_rows)?>);

			table.data('rowsId', []);

			<?php foreach($array_rows as $key => $row) { ?>
				table.data('rowsId').push(<?=JSHelper::cast($key)?>);
			<?php } ?>

			table.data('dataTable', dataTable);

			table.data('getRowData', function(id) {
				return table.data('rowsData')[id];
			});

			<?php foreach ($columns as $column): ?>
				<?=$column->call_load_render_function($_this)?>
			<?php endforeach; ?>

			<?php if($has_checkbox_column): ?>
				table.data('getCheckedRows', function(field) {
					var checkedInputs = table.find('tbody tr input.checkbox:checked');
					var rowsData = [];
					checkedInputs.each(function(index, item)
					{
						var id = $(this).val();
						var rowData = table.data('getRowData')(id);

						if(field)
						{
							rowData = rowData[field];
						}

						rowsData.push(rowData);
					});

					return rowsData;
				});
			<?php endif; ?>

			var dataTableWrapper = table.getParent('.dataTables_wrapper');
			var dataTablePaginationList = dataTableWrapper.find('.dataTables_paginate ul.pagination');

			if(dataTablePaginationList.children().length > 3)
			{
				dataTablePaginationList.show();
			}
			else
			{
				dataTablePaginationList.hide();
			}

			<?php if($reordering) { ?>

				$.each(table.data('rowsId'), function(index, rowId) {
					table.find('tbody tr').eq(index).attr('data-row-id', rowId);
				});

				table.addClass('sortable');

				table.find('tbody').sortable({
					'stop': function(evt, ui)
					{
						var rowsOrder = [];

						table.find('tbody tr').each(function(index, item) {
							rowsOrder.push($(item).attr('data-row-id'));
						});

						<?php if($reordering_callback) { ?>
							<?=$reordering_callback?>(rowsOrder);
						<?php } ?>
					}
				});

				table.find('tbody').disableSelection();

			<?php } ?>

			<?php if($clear_filters) { ?>
				dataTable.fnFilter('');
				dataTable.fnPageChange(0);
			<?php } ?>
		})();
	</script>

<?php endif; ?>