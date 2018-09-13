<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'faturamento-ambiente.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

$vColigadas = array(
						1 => 'DIARIO DO PARA',
						8 => 'DOL - INTERMEDIARIO DE NEGOCIOS E PORTAL DE INTERNET LTDA'
					);

foreach ( $vColigadas as $codigo => $descricao ) {
	$tpl->CODIGO = $codigo;
	$tpl->COLIGADA = utf8_decode ( $descricao );
	
	if ($_REQUEST ['coligada'] == $codigo) {
		$tpl->SELECIONADO = 'selected';
	} else {
		$tpl->clear ( 'SELECIONADO' );
	}
	
	$tpl->block ( 'BLOCK_COLIGADAS' );
}

$tpl->show ();

?>