$(document).ready(function(){
	$("select[name=coligada]").change(function(){

		$.post('movimentos.php',{coligada: $(this).val()},function(resp){
			$("#movimentos").html(resp);
		});

	});
})