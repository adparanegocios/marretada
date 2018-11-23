$(document).ready(function(){
	$("select[name=coligada]").change(function(){

		$.post('movimentos-compras.php',{coligada: $(this).val()},function(resp){
			$("#movimentos").html(resp);
		});

	});
})