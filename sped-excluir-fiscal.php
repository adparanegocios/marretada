<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'sped-excluir-fiscal.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

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

extract ( $_REQUEST );

if ($_REQUEST ['busca']) {
	
	$tpl->NOTA = $nota;
	
	if (! empty ( $coligada ) && ! empty ( $nota )) {
		
		$sql = "
	SELECT 
		m.IDMOV AS [IDENTIFICADOR],
		m.NUMEROMOV AS [NOTA],
		m.CODTMV AS [MOVIMENTO],
		f.NOME AS [FORNECEDOR],
		convert(VARCHAR(10),m.DATAEMISSAO,103) AS [EMISSAO],
		m.VALORLIQUIDO AS [VALOR],
		m.CODTDO AS [DOCUMENTO] 
	FROM TMOV m (nolock) 
	INNER JOIN DLAF d (nolock) ON (m.NUMEROMOV = d.DOCINI AND m.CODCOLIGADA = d.CODCOLIGADA AND m.IDMOV = d.IDMOV)
	INNER JOIN FCFO f (nolock) ON (f.CODCFO = m.CODCFO AND (f.CODCOLIGADA = m.CODCOLIGADA OR f.CODCOLIGADA = 0))
	WHERE 
		m.CODCOLIGADA = $coligada AND 
		m.NUMEROMOV LIKE '%$nota%';
	";
		
		$rs = $db->Execute ( $sql );
		
		if ($rs) {
			if ($rs->PO_RecordCount () > 0) {
				while ( $o = $rs->FetchNextObject () ) {
					$tpl->COLIGADA = $coligada;
					$tpl->IDENTIFICADOR = $o->IDENTIFICADOR;
					$tpl->NOTA = $o->NOTA;
					$tpl->MOVIMENTO = $o->MOVIMENTO;
					$tpl->FORNECEDOR = $o->FORNECEDOR;
					$tpl->EMISSAO = $o->EMISSAO;
					$tpl->DOCUMENTO = $o->DOCUMENTO;
					$tpl->block ( 'BLOCK_DADOS' );
				
				}
			} else {
				$tpl->block ( 'BLOCK_VAZIO' );
			}
		
		}
	} else {
		$tpl->block ( 'BLOCK_VAZIO' );
	}
} else {
	$tpl->block ( 'BLOCK_VAZIO' );
}

$tpl->show ();

?>