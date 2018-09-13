$(document).ready(function(){
	
	$('#entrada').focus(function(){
		$(this).calendario({ 
			target:'#entrada'
		});
	});
	
	$('#emissao').focus(function(){
		$(this).calendario({ 
			target:'#emissao'
		});
	});
	
	$('#pesquisar').click(function(){
		
		var dados = $('#rel-caixa').serialize();
		
		$.ajax({
			type: "POST",
			url: 'rel-caixa-filtro.php',
			data: dados,
			beforeSend: function (data) {
				$('#resultado').html('<center><br><br><br><br><br><img src="../img/ajax-loader.gif"><br><br><br><br><br></center>');
			},
			success: function(data){
				$('#resultado').html(data);
			}
		});
		
	});
	
});