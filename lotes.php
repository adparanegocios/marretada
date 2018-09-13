<?php

include_once 'global.php';

$tpl = new Template ( 'lotes.html' );

extract ( $_REQUEST );

$sql = "
SELECT 
	L.CODLOTE AS [CODIGOLOTE],
	'[LOTE ' + CAST (L.CODLOTE AS VARCHAR) + '] ' + L.DESCRICAO AS [DESCRICAOLOTE] 
FROM CLOTE L (NOLOCK)
WHERE
	L.CODCOLIGADA = $coligada AND
	(SELECT TOP 1 P.IDPARTIDA FROM CPARTIDA P (NOLOCK) WHERE P.CODCOLIGADA = L.CODCOLIGADA AND P.CODLOTE = L.CODLOTE ORDER BY P.DATA DESC) IS NOT NULL
";

$rs = $db->execute ( $sql );

while ( $o = $rs->FetchNextObject () ) {
	$tpl->VALUE = $o->CODIGOLOTE;
	$tpl->TEXT = utf8_encode ( $o->DESCRICAOLOTE );
	$tpl->block ( 'BLOCK_OPTION_LOTE' );
}

$tpl->show ();

?>