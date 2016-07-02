<?php if($message): ?>

	<script type="text/javascript">

		$(document).ready(function() {

			$.notify({
				'message': <?=JSHelper::cast_str($message)?>,
				'type': <?=JSHelper::cast_str($message_type)?>
			});

		});

	</script>

<?php endif; ?>
