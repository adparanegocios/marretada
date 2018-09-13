$(document).ready(function(){
	$("select[name=coligada]").change(function(){

		$("#lote").html('<option value="">Carregando...</option>');

		$.post('lotes.php',{coligada: $(this).val()},function(resp){
			$("#lote").html(resp);
		});

	});
})