<?php

session_start ();
include_once 'validacao.php';
include_once 'global.php';

$tpl = new Template ( 'excluir.html' );

extract ( $_REQUEST );

$usuario = $_SESSION ['usuario'];
$timestamp = mktime ( date ( "H" ) - 3, date ( "i" ), date ( "s" ), date ( "m" ), date ( "d" ), date ( "Y" ), 0 );
$data = gmdate ( "Y-m-d H:i:s", $timestamp );
$data_log = gmdate ( "d/m/Y H:i:s", $timestamp );

$referencia = (! empty ( $opcao ) && $opcao == 2) ? $pesquisa : "exc_$lancamento";

$sql = "
-- Disable foreign key constraint
ALTER TABLE " . NAME_BANCO . ".dbo.CLCOMPL NOCHECK CONSTRAINT FKCLCOMPL_CPARTIDA;
";

$sql .= "
DELETE FROM CCONT WHERE IDPARTIDA = $partida AND CODCOLIGADA = $coligada;
DELETE FROM CPARTIDA WHERE IDPARTIDA = $partida AND CODCOLIGADA = $coligada AND CODLOTE = $lote;
";

$sql .= "
-- Enable foreign key constraint
ALTER TABLE " . NAME_BANCO . ".dbo.CLCOMPL CHECK CONSTRAINT FKCLCOMPL_CPARTIDA;
";
//printvardie($sql);
$rs = $db->Execute ( $sql );

if ($rs) {
	$tpl->MSG = "
					<h1><font color = 'green'>Partida excluída com sucesso.</fonte></h1>
					<a href = 'lista-partidas.php?coligada=$coligada&lancamento=$referencia&opcao=$opcao&lote=$lote'>Voltar</a>		
				";
	// abre o arquivo colocando o ponteiro de escrita no final
	$nome = '';
	switch ($coligada) {
		case 1 :
			$nome = 'diario.txt';
			break;
		case 2 :
			$nome = 'rba.txt';
			break;
		case 3 :
			$nome = 'diariofm.txt';
			break;
		case 4 :
			$nome = '99fm.txt';
			break;
		case 5 :
			$nome = 'radioclube.txt';
			break;
		case 6 :
			$nome = 'sistemaclube.txt';
			break;
		case 7 :
			$nome = 'sistemanorte.txt';
			break;
		case 8 :
			$nome = 'dol.txt';
			break;
	}
	
	$arquivo = fopen ( $nome, 'a+' );
	
	if ($arquivo) {
		$conteudo = "Coligada $coligada: $data_log - $usuario excluiu a partida $partida do lancamento $lancamento. Computador: " . gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] ) . ". Ip: {$_SERVER['REMOTE_ADDR']}.\r\n";
		fwrite ( $arquivo, $conteudo );
		fclose ( $arquivo );
	}
} else {
	
	$tpl->MSG = "
					<h1><font color = 'red'>NÃO FOI POSSÍVEL EXCLUIR A PARTIDA. TENTE NOVAMENTE, POR FAVOR!</fonte></h1>
					<a href = 'lista-partidas.php?coligada=$coligada&lancamento=$referencia&opcao=$opcao&lote=$lote'>Voltar</a>
	";
}

$tpl->show ();

?>