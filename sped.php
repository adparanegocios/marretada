<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'sped.html' );

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
	p.NOMEFANTASIA AS [PRODUTO],
	c.NOME AS [NATUREZA],
	c.CODNAT AS [CODNATUREZA],
	mo.NOME AS [MOVIMENTOORIGINAL],
	d.BASEICMS AS [BASEICMS],
	d.FATORICMS AS [REDICMS],
	d.BASEREDICMS AS [BASETRIBUTADAICMS],
	d.ALIQICMS AS [ALIQICMS],
	d.VALORICMS AS [VALORICMS],
	d.ISENTOICMS AS [ISENTOICMS],
	d.OUTROSICMS AS [OUTROSICMS],
	d.BASEIPI AS [BASEIPI],
	d.VALORIPI AS [VALORIPITRIBUTADO],
	d.VALORIPISEMCREDITO AS [VALORIPINAOTRIBUTADO],
	d.ISENTOIPI AS [ISENTOIPI],
	d.OUTROSIPI AS [OUTROSIPI],
	d.VALORSUBST AS [VALORSUBSTITUICAO],
	d.VALORCONT AS [VALORCONTABIL],
	h.HISTORICOLONGO AS [HISTORICOLONGO],
	h.HISTORICOCURTO AS [HISTORICOCURTO],
	fi.DECLARACAOIMPORTACAO AS [DI] 
FROM TMOV m (nolock) 
INNER JOIN FCFO f (nolock) ON (f.CODCFO = m.CODCFO AND (f.CODCOLIGADA = m.CODCOLIGADA OR f.CODCOLIGADA = 0))
INNER JOIN TITMMOV i (nolock) ON (i.IDMOV = m.IDMOV AND i.CODCOLIGADA = m.CODCOLIGADA)
INNER JOIN TPRODUTO p (nolock) ON (p.IDPRD = i.IDPRD)
INNER JOIN DCFOP c (nolock) ON (c.IDNAT = m.IDNAT AND c.CODCOLIGADA = m.CODCOLIGADA)
INNER JOIN TTMV mo (nolock) ON (mo.CODTMV = m.CODTMV AND mo.CODCOLIGADA = m.CODCOLIGADA)
INNER JOIN DLAF d (nolock) ON (m.NUMEROMOV = d.DOCINI AND m.CODCOLIGADA = d.CODCOLIGADA AND m.IDMOV = d.IDMOV)
INNER JOIN TMOVHISTORICO h (nolock) ON (h.IDMOV = m.IDMOV AND h.CODCOLIGADA = m.CODCOLIGADA)
LEFT JOIN TMOVFISCAL fi (nolock) ON (fi.IDMOV = m.IDMOV AND fi.CODCOLIGADA = m.CODCOLIGADA)
WHERE 
	m.CODCOLIGADA = $coligada AND 
	m.IDMOV = '$identificador' AND
	i.IDPRD = '$produto'
";

$rs = $db->Execute ( $sql );

if ($rs) {
	while ( $o = $rs->FetchNextObject () ) {
		
		foreach ( $vColigadas as $indice => $descricao ) {
			if ($indice == $coligada) {
				$tpl->NOMECOLIGADA = utf8_decode ( $descricao );
				break;
			}
		}
		
		$tpl->COLIGADA = $coligada;
		$tpl->IDENTIFICADOR = $o->IDENTIFICADOR;
		$tpl->NOTA = $o->NOTA;
		$tpl->MOVIMENTO = $o->MOVIMENTO;
		$tpl->FORNECEDOR = $o->FORNECEDOR;
		$tpl->EMISSAO = $o->EMISSAO;
		$tpl->VALOR = number_format ( $o->VALOR, 2, ',', '.' );
		$tpl->CHAVE = $o->CHAVE;
		$tpl->CODIGO = $o->CODIGO;
		$tpl->DOCUMENTO = $o->DOCUMENTO;
		$tpl->ENTRADA = Util::converteData ( $o->ENTRADA );
		$tpl->IDENTIFICADORPRODUTO = $o->IDENTIFICADORPRODUTO;
		$tpl->CODIGOPRODUTO = $o->CODIGOPRODUTO;
		$tpl->PRODUTO = $o->PRODUTO;
		$tpl->NATUREZA = $o->NATUREZA;
		$tpl->CODNATUREZA = $o->CODNATUREZA;
		$tpl->MOVIMENTOORIGINAL = $o->MOVIMENTOORIGINAL;
		$tpl->BASEICMS = number_format ( $o->BASEICMS, 2, ',', '.' );
		$tpl->REDICMS = number_format ( $o->REDICMS, 2, ',', '.' );
		$tpl->BASETRIBUTADAICMS = number_format ( $o->BASETRIBUTADAICMS, 2, ',', '.' );
		$tpl->ALIQICMS = number_format ( $o->ALIQICMS, 2, ',', '.' );
		$tpl->VALORICMS = number_format ( $o->VALORICMS, 2, ',', '.' );
		$tpl->ISENTOICMS = number_format ( $o->ISENTOICMS, 2, ',', '.' );
		$tpl->OUTROSICMS = number_format ( $o->OUTROSICMS, 2, ',', '.' );
		$tpl->BASEIPI = number_format ( $o->BASEIPI, 2, ',', '.' );
		$tpl->VALORIPITRIBUTADO = number_format ( $o->VALORIPITRIBUTADO, 2, ',', '.' );
		$tpl->VALORIPINAOTRIBUTADO = number_format ( $o->VALORIPINAOTRIBUTADO, 2, ',', '.' );
		$tpl->ISENTOIPI = number_format ( $o->ISENTOIPI, 2, ',', '.' );
		$tpl->OUTROSIPI = number_format ( $o->OUTROSIPI, 2, ',', '.' );
		$tpl->VALORSUBSTITUICAO = number_format ( $o->VALORSUBSTITUICAO, 2, ',', '.' );
		$tpl->VALORCONTABIL = number_format ( $o->VALORCONTABIL, 2, ',', '.' );
		$tpl->HISTORICO = (! empty ( $o->HISTORICOLONGO )) ? $o->HISTORICOLONGO : $o->HISTORICOCURTO;
		$tpl->DI = $o->DI;
		$tpl->TIPOHISTORICO = (! empty ( $o->HISTORICOLONGO )) ? 'longo' : 'curto';
	}
}

$tpl->show ();

?>