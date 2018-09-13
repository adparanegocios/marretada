<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';
//printvardie($_REQUEST);
$tpl = new Template ( 'sped-processa.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

$usuario = $_SESSION ['usuario'];

extract ( $_REQUEST );
$entrada_banco = Util::converteDataBanco ( $entrada_banco );
$entrada = Util::converteDataBanco ( $entrada );
$emissao = Util::converteDataBanco ( $emissao );

$baseicms = (empty ( $baseicms )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $baseicms ) );
$basetributadaicms = (empty ( $basetributadaicms )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $basetributadaicms ) );
$valoricms = (empty ( $valoricms )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $valoricms ) );
$isentoicms = (empty ( $isentoicms )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $isentoicms ) );
$outrosicms = (empty ( $outrosicms )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $outrosicms ) );
$baseipi = (empty ( $baseipi )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $baseipi ) );
$valoripitributado = (empty ( $valoripitributado )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $valoripitributado ) );
$valoripinaotributado = (empty ( $valoripinaotributado )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $valoripinaotributado ) );
$isentoipi = (empty ( $isentoipi )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $isentoipi ) );
$outrosipi = (empty ( $outrosipi )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $outrosipi ) );
$valorsubstituicao = (empty ( $valorsubstituicao )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $valorsubstituicao ) );
$valorcontabil = (empty ( $valorcontabil )) ? 0 : str_replace ( ',', '.', str_replace ( '.', '', $valorcontabil ) );

$erro = false;
$msg = '';

if (! empty ( $salvar )) {
	if (! empty ( $chave ) && (is_numeric ( $chave ) == false || strlen ( $chave ) != 44)) {
		$msg .= "<font color = 'red'>Chave de Acesso inválida!</font><br />";
		$erro = true;
	}
	
	if (empty ( $codigo )) {
		$msg .= "<font color = 'red'>Código do Fornecedor não pode ser vazio!</font><br />";
		$erro = true;
	}
	
	if (! empty ( $documento ) && strlen ( $documento ) != 5) {
		$msg .= "<font color = 'red'>Tipo de Documento inválido!</font><br />";
		$erro = true;
	}
	
	if (empty ( $documento )) {
		$msg .= "<font color = 'red'>Tipo de Documento não pode ser vazio!</font><br />";
		$erro = true;
	}
	
	if (empty ( $codigoproduto )) {
		$msg .= "<font color = 'red'>Cógido do Produto não pode ser vazio!</font><br />";
		$erro = true;
	}
	
	if (empty ( $codnatureza )) {
		$msg .= "<font color = 'red'>Cógido da Natureza não pode ser vazio!</font><br />";
		$erro = true;
	}
	
	if (empty ( $historico )) {
		$msg .= "<font color = 'red'>Histórico não pode ser vazio!</font><br />";
		$erro = true;
	}
	
	if ($erro) {
		$tpl->VOLTAR = "<button onclick='goBack()'>Voltar</button>";
	} else {
		$codigo = strtoupper ( $codigo );
		$sql = "
		SET DATEFORMAT ymd;
		
		DECLARE @identificador INT = '$identificador';
		DECLARE @coligada INT = $coligada;
		DECLARE @idlaf INT = (SELECT IDLAF FROM DLAF WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador);
		DECLARE @codnat VARCHAR(8) = '$codnatureza'; 
		DECLARE @idnat INT = (SELECT IDNAT FROM DNATUREZA WHERE CODCOLIGADA = @coligada AND CODNAT = @codnat);
		
		UPDATE 
			TMOV 
		SET
		";
		
		if ($codnat_banco != $codnatureza) {
			$sql .= "
				IDNAT = @idnat,
				CODLAFE = NULL,
			";
		}
		
		$sql .= "
			CHAVEACESSONFE = '$chave',
			CODCFO = '$codigo',
			CODTDO = '$documento',
			DATASAIDA = '$entrada',
			DATAEMISSAO = '$emissao'
		WHERE 
			CODCOLIGADA = @coligada AND 
			IDMOV = @identificador AND 
			CODTMV = '$movimento';
			
		UPDATE TITMMOV SET IDPRD = (SELECT IDPRD FROM TPRODUTO (NOLOCK) WHERE CODIGOPRD = '$codigoproduto') WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador AND IDPRD = '$identificadorproduto';
		";
		
		if ($tipohistorico == 'longo') {
			$sql .= "
				UPDATE TMOVHISTORICO SET HISTORICOLONGO = '$historico' WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;
				UPDATE TITMMOVHISTORICO SET HISTORICOLONGO = '$historico' WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;		
			";
		} elseif ($tipohistorico == 'curto') {
			$sql .= "
				UPDATE TMOVHISTORICO SET HISTORICOCURTO = '$historico' WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;
				UPDATE TITMMOVHISTORICO SET HISTORICOCURTO = '$historico' WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;		
			";
		}
		
		if ($codnat_banco != $codnatureza) {
			$sql .= "
				UPDATE TITMMOV SET IDNAT = @idnat WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;
				
				DELETE FROM DTRBLAF WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DTRBITEM WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DHISTITEM WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DITEMCOMPL WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAFTRANSP WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAFRATCCU WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAFCOMPL WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAFCOMPLEMENTOCTRC WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DITEM WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DHISTLAF WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAFEXTERIOR WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;
				DELETE FROM DLAF WHERE CODCOLIGADA = @coligada AND IDLAF = @idlaf;	
			";
		} else {
			$sql .= "
		UPDATE 
			DLAF 
		SET
			CHAVEACESSONFE = '$chave',
			CODCFO = '$codigo',
			CODTDO = '$documento',
			DATAES = '$entrada',
			DATAEMISSAO = '$emissao',
			DATALF = '$entrada',
			BASEICMS = $baseicms,
			BASEREDICMS = $basetributadaicms,
			VALORICMS = $valoricms,
			ISENTOICMS = $isentoicms,
			OUTROSICMS = $outrosicms,
			BASEIPI = $baseipi,
			VALORIPI = $valoripitributado,
			VALORIPISEMCREDITO = $valoripinaotributado,
			ISENTOIPI = $isentoipi,
			OUTROSIPI = $outrosipi,
			VALORSUBST = $valorsubstituicao,
			VALORCONT = $valorcontabil
		WHERE 
			CODCOLIGADA = @coligada AND 
			IDMOV = @identificador;
			
		UPDATE DITEM SET IDPRD = (SELECT IDPRD FROM TPRODUTO (NOLOCK) WHERE CODIGOPRD = '$codigoproduto') WHERE CODCOLIGADA = @coligada AND IDLAF = (SELECT IDLAF FROM DLAF (NOLOCK) WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador) AND IDPRD = '$identificadorproduto';
		";
		
		}
		
		if (! empty ( $di )) {
			$sql .= "
				UPDATE TMOVFISCAL SET DECLARACAOIMPORTACAO = '$di' WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;
				UPDATE DLAFEXTERIOR SET NUMERODI = '$di' WHERE CODCOLIGADA = @coligada AND IDLAF = (SELECT IDLAF FROM DLAF (NOLOCK) WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador);
			";
		}
		
		//printvardie ( $sql );
		$db->Execute ( $sql );
		$affected = $db->Affected_Rows ();
		
		if ($affected > 0) {
			$msg = "<font color = 'green'>Dados alterados com sucesso.</font>";
			$tpl->VOLTAR = "<button onclick='goBack2()'>Voltar</button>";
		}
	
	}
	
	$tpl->MSG = $msg;

}

$tpl->show ();

?>