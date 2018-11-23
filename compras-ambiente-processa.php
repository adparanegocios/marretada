<?php

require_once 'classes/phpmailer/class.phpmailer.php';

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'compras-ambiente-processa.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

$usuario = $_SESSION ['usuario'];
$timestamp = mktime ( date ( "H" ) - 3, date ( "i" ), date ( "s" ), date ( "m" ), date ( "d" ), date ( "Y" ), 0 );
$data = gmdate ( "d/m/Y H:i:s", $timestamp );
$itens = '';
$sql = '';
$tipo = '';
$ativo = '';
$affected = '';
$vetMovimentos = array ();

//printvardie ( $_REQUEST );
extract ( $_REQUEST );

if (! empty ( $coligada ) && sizeof ( $movimentos ) > 0 && ! empty ( $ambiente )) {
	foreach ( $movimentos as $m ) {
		$vetMovimentos [] = $m;
		list ( $m, $d ) = explode ( "=>", $m );
		$itens .= "'$m', ";
	}
	
	$itens = substr ( $itens, 0, - 2 );
	
	$ativo = ($ambiente == '1')?1:0;
	
	$sql = "
	UPDATE
		I
	SET
		I.HABILITAORCAMENTO = $ativo
	FROM TTMV M (NOLOCK)
	INNER JOIN TITMTMV I (NOLOCK) ON (I.CODTMV = M.CODTMV AND I.CODCOLIGADA = M.CODCOLIGADA)
	WHERE
		M.CODCOLIGADA IN ($coligada) AND
		M.CODTMV IN ($itens)
	";
	
	$db->Execute ( $sql );
	$affected = $db->Affected_Rows ();
	
	if ($affected > 0) {
		
		$mail = new PHPMailer ();
		$mail -> charSet = "UTF-8";
		$mail->SetLanguage ( "br", "../classes/phpmailer/language/" );
		$mail->IsSMTP ();
		$mail->Host = HOST_EMAIL;
		$mail->SMTPAuth = true;
		$mail->Port = PORT_EMAIL;
		$mail->Username = USER_EMAIL;
		$mail->Password = PASS_EMAIL;
		$mail->From = USER_EMAIL;
		$mail->AddReplyTo ( USER_EMAIL );
		$mail->FromName = "RM TOTVS";
		$mail->WordWrap = 50;
		$mail->IsHTML ( true );
		
		$assunto = ($coligada == 1) ? "[DIÁRIO DO PARÁ] " : "[DOL] ";
		$assunto .= strtoupper ( $_SESSION ['usuario'] ) . " colocou movimento(s) de COMPRAS em ";
		$assunto .= ($ambiente == 1) ? "PRODUÇÃO" : "EDIÇÃO";
		$assunto .= " - $data.";
		
		$mail->Subject = utf8_decode($assunto);
		
		$conteudo = "
		<style type='text/css'>
			<!--
				.style1 {color: #FF0000}
			-->
		</style>
		";
		
		$conteudo .= "<p>Movimento(s) atualizado(s):</p>";
		
		foreach ( $vetMovimentos as $v ) {
			$conteudo .= "- ".utf8_encode($v)."<br />";
		}
		
		$conteudo .= "<p><span class='style1'>Por favor, <em><strong>SAIA</strong></em> do <em><strong>SISTEMA RM TOTVS</strong></em> e <em><strong>ENTRE</strong></em> novamente para que as altera&ccedil;&otilde;es surtam efeito.</span> </p>";
		
		$mail->Body = utf8_decode ( $conteudo );
		$mail->AddAddress ( 'erp@rbadecomunicacao.com.br' );
		$mail->AddAddress ( 'ladilene.martins@rbadecomunicacao.com.br' );
		$mail->AddAddress ( 'lvilhena@rbadecomunicacao.com.br' );
		$mail->AddAddress ( 'etiene.oliveira@diariodopara.com.br' );
		$mail->AddAddress ( 'ivone.castro@diariodopara.com.br' );
		$mail->AddAddress ( 'gilsonmatos@rbadecomunicacao.com.br' );
		$mail->AddAddress ( 'carmem.rodrigues@diariodopara.com.br' );
		$mail->Send ();
		$mail->SmtpClose ();
		
		$tpl->MSG = "
					<h1><font color = 'green'>SUCESSO: ambiente atualizado. SAIR e ENTRAR no SISTEMA RM TOTVS novamente.</fonte></h1>		
				";
	}

} else {
	$tpl->MSG = "
					<h1><font color = 'red'>ERRO: POR FAVOR, ESCOLHA A COLIGADA, MOVIMENTO(S) E O AMBIENTE DO FAUTRAMENTO!</fonte></h1>
	";
}

$tpl->show ();

?>