<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'edita-partida.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

extract ( $_REQUEST );

$situacao = ($situacao == 'd') ? 'DEBITO' : 'CREDITO';
$tpl->SITUACAO = $situacao;
$tpl->REFERENCIA = $referencia;
$tpl->CODIGOC = $coligada;
$tpl->CODLOTE = $lote;
$tpl->OPCAO = $opcao;
$tpl->PESQUISA = $pesquisa;
$tpl->VALORPESQUISA = (! empty ( $pesquisa )) ? $pesquisa : $referencia;

foreach ( $vColigadas as $indice => $descricao ) {
	if ($indice == $coligada) {
		$tpl->COLIGADA = utf8_decode ( $descricao );
		break;
	}
}

$sql = "
SELECT 
	'[LOTE ' + CAST (L.CODLOTE AS VARCHAR) + '] ' + L.DESCRICAO AS [DESCRICAOLOTE]  
FROM CLOTE L (NOLOCK)
WHERE
	L.CODCOLIGADA = $coligada AND
	L.CODLOTE = $lote
";

$rs = $db->execute ( $sql );
$tpl->LOTE = $rs->FetchNextObject ()->DESCRICAOLOTE;

$sql = "
SELECT 
	p.DATA AS [data],
	p.IDPARTIDA AS [partida],
	p.{$situacao} AS [conta],
	p.COMPLEMENTO AS [complemento],
	(SELECT c.REDUZIDO FROM CCONTA c (nolock) WHERE c.CODCOLIGADA = $coligada AND c.CODCONTA = p.{$situacao}) AS [reduzido],
	upper((SELECT c.DESCRICAO FROM CCONTA c (nolock) WHERE c.CODCOLIGADA = $coligada AND c.CODCONTA = p.{$situacao})) AS [descricao]
FROM CPARTIDA p (nolock)
WHERE
	p.CODCOLIGADA = $coligada AND
	p.IDPARTIDA = $partida
";

$rs = $db->Execute ( $sql );

if ($rs) {
	$tpl->LANCAMENTO = $lancamento;
	$tpl->SITUACAO = $situacao;
	
	$sql = "SELECT TOP 1 c.NOMEFANTASIA AS [CLIENTE] FROM FCFO c (nolock) WHERE c.CODCFO = '$codigo' AND c.CODCOLIGADA = $coligada";
	$rs_aux = $db->Execute ( $sql );
	$cliente = $rs_aux->FetchNextObject ()->CLIENTE;
	
	while ( $o = $rs->FetchNextObject () ) {
		$tpl->CLIENTE = $cliente;
		$tpl->DEFAULT = $default;
		$tpl->PARTIDA = $o->PARTIDA;
		$tpl->REFERENCIA = $referencia;
		$tpl->CONTA = $o->CONTA;
		$tpl->REDUZIDO = $o->REDUZIDO;
		$tpl->DESCRICAO = $o->DESCRICAO;
		$tpl->COMPLEMENTO = $o->COMPLEMENTO;
		$tpl->VALOR = $valor;
		$tpl->DATA = Util::converteData ( $o->DATA );
	}
}

$tpl->show ();

?>