<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'sped-lista-produto.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

extract ( $_REQUEST );

$sql = "
	SELECT 
		m.CODCOLIGADA AS [COLIGADA],
		m.IDMOV as [IDENTIFICADOR],
		m.NUMEROMOV AS [NOTA],
		m.CHAVEACESSONFE AS [CHAVE],
		m.CODTMV AS [MOVIMENTO],
		m.CODCFO AS [CODIGO],
		f.NOME AS [FORNECEDOR],
		convert(VARCHAR(10),m.DATAEMISSAO,103) AS [EMISSAO],
		i.VALORBRUTOITEM AS [VALOR],
		m.CODTDO AS [DOCUMENTO],
		m.DATASAIDA AS [ENTRADA],
		p.IDPRD AS [IDENTIFICADORPRODUTO],
		p.CODIGOPRD AS [CODIGOPRODUTO],
		p.NOMEFANTASIA AS [PRODUTO] 
	FROM TMOV m (nolock) 
	INNER JOIN FCFO f (nolock) ON (f.CODCFO = m.CODCFO AND (f.CODCOLIGADA = m.CODCOLIGADA OR f.CODCOLIGADA = 0))
	INNER JOIN TITMMOV i (nolock) ON (i.IDMOV = m.IDMOV AND i.CODCOLIGADA = m.CODCOLIGADA)
	INNER JOIN TPRODUTO p (nolock) ON (p.IDPRD = i.IDPRD)
	WHERE 
		m.CODCOLIGADA = $coligada AND 
		m.IDMOV = '$identificador'
	";

$rs = $db->Execute ( $sql );

if ($rs) {
	while ( $o = $rs->FetchNextObject () ) {
		$tpl->COLIGADA = $coligada;
		$tpl->IDENTIFICADOR = $o->IDENTIFICADOR;
		$tpl->NOTA = $o->NOTA;
		$tpl->MOVIMENTO = $o->MOVIMENTO;
		$tpl->FORNECEDOR = $o->FORNECEDOR;
		$tpl->VALOR = number_format ( $o->VALOR, 2, ',', '.' );
		$tpl->IDENTIFICADORPRODUTO = $o->IDENTIFICADORPRODUTO;
		$tpl->PRODUTO = $o->PRODUTO;
		$tpl->block ( 'BLOCK_DADOS' );
	}
}

$tpl->show ();

?>