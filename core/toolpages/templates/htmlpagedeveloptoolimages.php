<style type="text/css">

	legend
	{
		font-weight: bold;
		border-bottom: solid 1px #000;
	}

	form .buttons
	{
		border-top: solid 1px #999;
		padding-top: 20px;
		margin-top: 20px;
	}

	form .buttons button span
	{
		padding: 5px 20px !important;
	}

</style>

<br />
<br />

<form id="image-form" class="form-ajax" action="!HTMLPageDevelopToolImages(add_image)" method="post">

	<div class="success-message alert alert-info hide">
		<span>El código de la imagen es:</span>
		<span class="id_image_file strong"></span>
	</div>

	<fieldset>

		<legend>Agregar Imagen</legend>

		<?=$input?>

	</fieldset>

	<div class="buttons text-left">
		<br />
		<button type="submit" class="btn btn-success"><span>Aceptar</span></button>
	</div>

</form>

<script type="text/javascript">

	var successMessage = $('#image-form').find('.success-message');

	$('#image-form').on('submit', function(evt) {
		successMessage.addClass('hide');
	});

	$('#image-form').on('success', function(evt, data) {
		successMessage.removeClass('hide');
		$(this).find('.success-message .id_image_file').text(data['id_image_file']);
		return false;
	});

</script>