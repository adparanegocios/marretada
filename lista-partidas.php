<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'lista-partidas.html' );

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

$coligada = $_REQUEST ['coligada'];
$lancamento = $_REQUEST ['lancamento'];
$opcao = $_REQUEST ['opcao'];
$lote = $_REQUEST ['lote'];

if (! empty ( $coligada )) {
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
		$tpl->TEXT = $o->DESCRICAOLOTE;
		
		if ($lote == $o->CODIGOLOTE) {
			$tpl->SELECTED_OPCAO = 'selected';
		} else {
			$tpl->clear ( 'SELECTED_OPCAO' );
		}
		
		$tpl->block ( 'BLOCK_OPTION_LOTE' );
	}
}

if ($opcao == 1) {
	$tpl->SELECTED_OPCAO1 = 'selected';
} elseif ($opcao == 2) {
	$tpl->SELECTED_OPCAO2 = 'selected';
} else {
	$tpl->clear ( 'SELECTED_OPCAO1' );
	$tpl->clear ( 'SELECTED_OPCAO2' );
}

if (($_REQUEST ['busca']) or (! empty ( $coligada ) && ! empty ( $lancamento ))) {
	
	$tpl->CODIGOC = $coligada;
	$tpl->LANCAMENTOCAMPO = $lancamento;
	
	if (! empty ( $_REQUEST ['lancamento'] ) && ! empty ( $_REQUEST ['coligada'] )) {
		extract ( $_REQUEST );
		
		// recuperando todas as contas débito de um lançamento
		$sql_d = "
		SELECT 
			P.DATA AS [DATA],
			P.LCTREF AS [REFERENCIA],
			P.VALOR AS [VALOR],
			P.COMPLEMENTO AS [COMPLEMENTO],
			P.IDLANCAMENTO AS [LANCAMENTO],
			P.IDPARTIDA AS [PARTIDA],
			P.CODLOTE AS [LOTE],
			P.CODLOTEORIGEM AS [ORIGEM],
			P.DOCUMENTO AS [DOCUMENTO],
			CONVERT(VARCHAR(10),P.DATA,103) AS [DATA],
			P.DEBITO AS [CONTA],
			(SELECT C.REDUZIDO FROM CCONTA C (NOLOCK) WHERE C.CODCOLIGADA = P.CODCOLIGADA AND C.CODCONTA = P.DEBITO) AS [REDUZIDO],
			UPPER((SELECT C.DESCRICAO FROM CCONTA C (NOLOCK) WHERE C.CODCOLIGADA = P.CODCOLIGADA AND C.CODCONTA = P.DEBITO)) AS [DESCRICAO]
		FROM CPARTIDA P (NOLOCK) 
		WHERE 
			P.CODCOLIGADA = $coligada AND
		";
		
		if ($opcao == 1) {
			if (strstr ( $lancamento, '_' )) {
				list ( $a, $b ) = explode ( '_', $lancamento );
				$sql_d .= "
			P.IDLANCAMENTO = $b AND
			";
			} else {
				$sql_d .= "
			P.IDLANCAMENTO IN (SELECT A.IDLANCAMENTO FROM CPARTIDA A (NOLOCK) WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.IDPARTIDA = $lancamento AND A.CODLOTE = P.CODLOTE) AND
			";
			}
		}
		
		if ($opcao == 2) {
			$sql_d .= "
			P.DOCUMENTO = '$lancamento' AND
			";
		}
		
		$sql_d .= "	
			P.DEBITO IS NOT NULL			
		";
		
		if ($lote != '') {
			$sql_d .= " AND P.CODLOTE = $lote";
		}
		//printvardie($sql_d);
		$rs = $db->Execute ( $sql_d );
		
		if ($rs) {
			while ( $o = $rs->FetchNextObject () ) {
				
				$documento = $o->COMPLEMENTO;
				$documento = preg_replace ( "/[^0-9]/i", " ", $documento );
				$documento = str_replace ( ' ', '', $documento );
				
				$sql_aux_d = "
			SELECT 
				cf.CODCFO AS [codigo],
				cf.NOME AS [cliente],
				(
					SELECT 
						pc.REDUZIDO 
					FROM CCONTA pc (nolock) 
					WHERE 
						pc.CODCOLIGADA = $coligada AND 
						pc.CODCONTA = (
							SELECT dc.CODCONTA FROM FCFOCONT dc WHERE dc.CODCFO = cf.CODCFO AND dc.CODCOLIGADA = $coligada AND dc.TIPO = 1
						)
				) AS [default] 
			FROM FCFO cf (nolock) 
			WHERE 
				cf.CODCOLIGADA = $coligada AND
				cf.CODCFO = (
					SELECT TOP 1 f.CODCFO FROM FLAN f (nolock) WHERE f.CODCOLIGADA = $coligada AND f.NUMERODOCUMENTO = '$documento'	
				)
			";
				
				$rs_aux = $db->Execute ( $sql_aux_d );
				
				if ($rs_aux) {
					while ( $aux = $rs_aux->FetchNextObject () ) {
						$tpl->CODIGO_D = $aux->CODIGO;
						$tpl->CLIENTE_D = $aux->CLIENTE;
						$tpl->DEFAULT_D = $aux->DEFAULT;
						$default_d = $aux->DEFAULT;
					}
				
				}
				
				$tpl->REFERENCIA_D = $o->REFERENCIA;
				$tpl->VALOR_D = number_format ( $o->VALOR, 2, ',', '.' );
				$tpl->LANCAMENTO_D = $o->LANCAMENTO;
				$tpl->PARTIDA_D = $o->PARTIDA;
				$tpl->DATA_D = $o->DATA;
				$tpl->CONTA_D = $o->CONTA;
				$tpl->REDUZIDO_D = $o->REDUZIDO;
				$tpl->DESCRICAO_D = $o->DESCRICAO;
				$tpl->LOTE_D = $o->LOTE;
				$tpl->OPCAO_D = $opcao;
				$tpl->PESQUISA_D = $lancamento;
				$tpl->block ( 'BLOCK_DEBITO' );
			}
		}
		
		// recuperando todas as contas crédito de um lançamento
		$sql_c = "
		SELECT 
			P.DATA AS [DATA],
			P.LCTREF AS [REFERENCIA],
			P.VALOR AS [VALOR],
			P.COMPLEMENTO AS [COMPLEMENTO],
			P.IDLANCAMENTO AS [LANCAMENTO],
			P.IDPARTIDA AS [PARTIDA],
			P.CODLOTE AS [LOTE],
			P.CODLOTEORIGEM AS [ORIGEM],
			P.DOCUMENTO AS [DOCUMENTO],
			CONVERT(VARCHAR(10),P.DATA,103) AS [DATA],
			P.CREDITO AS [CONTA],
			(SELECT C.REDUZIDO FROM CCONTA C (NOLOCK) WHERE C.CODCOLIGADA = P.CODCOLIGADA AND C.CODCONTA = P.CREDITO) AS [REDUZIDO],
			UPPER((SELECT C.DESCRICAO FROM CCONTA C (NOLOCK) WHERE C.CODCOLIGADA = P.CODCOLIGADA AND C.CODCONTA = P.CREDITO)) AS [DESCRICAO]
		FROM CPARTIDA P (NOLOCK) 
		WHERE 
			P.CODCOLIGADA = $coligada AND
		";
		
		if ($opcao == 1) {
			
			if (strstr ( $lancamento, '_' )) {
				list ( $a, $b ) = explode ( '_', $lancamento );
				$sql_c .= "
			P.IDLANCAMENTO = $b AND
			";
			} else {				
				$sql_c .= "
			P.IDLANCAMENTO IN (SELECT A.IDLANCAMENTO FROM CPARTIDA A (NOLOCK) WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.IDPARTIDA = $lancamento AND A.CODLOTE = P.CODLOTE) AND
			";
			}
		}
		
		if ($opcao == 2) {
			$sql_c .= "
			P.DOCUMENTO = '$lancamento' AND
			";
		}
		
		$sql_c .= "
			P.CREDITO IS NOT NULL		
		";
		
		if ($lote != '') {
			$sql_c .= " AND P.CODLOTE = $lote";
		}
		
		//printvardie($sql_c);
		$rs = $db->Execute ( $sql_c );
		
		if ($rs) {
			while ( $o = $rs->FetchNextObject () ) {
				
				$documento = $o->COMPLEMENTO;
				$documento = preg_replace ( "/[^0-9]/i", " ", $documento );
				$documento = str_replace ( ' ', '', $documento );
				
				$sql_aux_c = "
			SELECT 
				cf.CODCFO AS [codigo],
				cf.NOME AS [cliente],
				(
					SELECT 
						pc.REDUZIDO 
					FROM CCONTA pc (nolock) 
					WHERE 
						pc.CODCOLIGADA = $coligada AND 
						pc.CODCONTA = (
							SELECT dc.CODCONTA FROM FCFOCONT dc WHERE dc.CODCFO = cf.CODCFO AND dc.CODCOLIGADA = $coligada AND dc.TIPO = 2
						)
				) AS [default] 
			FROM FCFO cf (nolock) 
			WHERE 
				cf.CODCOLIGADA = $coligada AND
				cf.CODCFO = (
					SELECT TOP 1 f.CODCFO FROM FLAN f (nolock) WHERE f.CODCOLIGADA = $coligada AND f.NUMERODOCUMENTO = '$documento'	
				)
			";
				
				$rs_aux = $db->Execute ( $sql_aux_c );
				
				if ($rs_aux) {
					while ( $aux = $rs_aux->FetchNextObject () ) {
						$tpl->CODIGO_C = $aux->CODIGO;
						$tpl->CLIENTE_C = $aux->CLIENTE;
						$tpl->DEFAULT_C = $aux->DEFAULT;
						$default_d = $aux->DEFAULT;
					}
				
				}
				
				$tpl->REFERENCIA_C = $o->REFERENCIA;
				$tpl->VALOR_C = number_format ( $o->VALOR, 2, ',', '.' );
				$tpl->LANCAMENTO_C = $o->LANCAMENTO;
				$tpl->PARTIDA_C = $o->PARTIDA;
				$tpl->DATA_C = $o->DATA;
				$tpl->CONTA_C = $o->CONTA;
				$tpl->REDUZIDO_C = $o->REDUZIDO;
				$tpl->DESCRICAO_C = $o->DESCRICAO;
				$tpl->LOTE_C = $o->LOTE;
				$tpl->OPCAO_C = $opcao;
				$tpl->PESQUISA_C = $lancamento;
				$tpl->block ( 'BLOCK_CREDITO' );
			}
		}
	
	}

}

$tpl->show ();

?>