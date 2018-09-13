<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'edita-partida-processa.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

$usuario = $_SESSION ['usuario'];
$timestamp = mktime ( date ( "H" ) - 3, date ( "i" ), date ( "s" ), date ( "m" ), date ( "d" ), date ( "Y" ), 0 );
$data = gmdate ( "Y-m-d H:i:s", $timestamp );
$data_log = gmdate ( "d/m/Y H:i:s", $timestamp );

extract ( $_REQUEST );
$data_banco = Util::converteDataBanco ( $data1 );
$valor_banco = $valor;
$valor_banco = str_replace ( '.', '', $valor_banco );
$valor_banco = str_replace ( ',', '.', $valor_banco );

$referencia = (! empty ( $opcao ) && $opcao == 2) ? $pesquisa : $referencia;

// alterando a conta de uma partida
$sql = "
SET DATEFORMAT ymd;

UPDATE
	CPARTIDA
SET
";
if (! empty ( $reduzido )) {
	$sql .= "
		CPARTIDA.{$situacao} = (SELECT c.CODCONTA FROM CCONTA c (nolock) WHERE c.REDUZIDO = $reduzido AND c.CODCOLIGADA = $coligada),
		CPARTIDA.CODCOL{$situacao} = (SELECT c.CODCOLIGADA FROM CCONTA c (nolock) WHERE c.REDUZIDO = $reduzido AND c.CODCOLIGADA = $coligada),
		";
}

if (! empty ( $data_banco )) {
	$sql .= "CPARTIDA.DATA = '$data_banco',";
}

if (! empty ( $complemento )) {
	$complemento = str_replace ( "'", "", $complemento );
	$sql .= "CPARTIDA.COMPLEMENTO = '$complemento',";
}

if (! empty ( $valor_banco )) {
	$sql .= "CPARTIDA.VALOR = '$valor_banco',";
}

$sql .= "	
	CPARTIDA.RECMODIFIEDBY = '$usuario',
	CPARTIDA.RECMODIFIEDON = '$data'
WHERE
	CPARTIDA.IDPARTIDA = $partida  AND 
	CPARTIDA.CODLOTE = $lote AND
	CPARTIDA.CODCOLIGADA = $coligada
";
//printvardie ( $sql );
$rs = $db->Execute ( $sql );
$affected = $db->Affected_Rows ();

if ($affected > 0) {
	
	$tpl->MSG = "
					<h1><font color = 'green'>Conta alterada com sucesso.</fonte></h1>
					<a href = 'lista-partidas.php?coligada=$coligada&lancamento=$partida&opcao=$opcao&lote=$lote'>Voltar</a>		
				";
	
	if (($data1 != $data_original) or (! empty ( $reduzido )) or ($valor_original != $valor) or ($complemento != $complemento_original)) {
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
			if ($data1 != $data_original) {
				$texto_data = "data de contabiliza��o de $data_original para $data1";
			} else {
				$texto_data = "";
			}
			
			if (! empty ( $reduzido )) {
				$texto_reduzido = "codigo reduzido de $reduzido_antigo para $reduzido";
			} else {
				$texto_reduzido = "";
			}
			
			if ($complemento != $complemento_original) {
				$texto_complemento = "historico do lancamento de " . '"' . $complemento_original . '"' . " para " . '"' . $complemento . '"';
			} else {
				$texto_complemento = "";
			}
			
			if ($valor_original != $valor) {
				$texto_valor = "valor de R$ $valor_original para R$ $valor";
			} else {
				$texto_valor = "";
			}
			
			$conteudo = "Coligada $coligada: $data_log - $usuario alterou $texto_reduzido $texto_data $texto_valor $texto_complemento referente a partida $partida do lancamento $lancamento. Computador: " . gethostbyaddr ( $_SERVER ['REMOTE_ADDR'] ) . ". Ip: {$_SERVER['REMOTE_ADDR']}.\r\n";
			
			fwrite ( $arquivo, $conteudo );
			fclose ( $arquivo );
		}
	
	}
} else {
	$tpl->MSG = "
					<h1><font color = 'red'>N�O FOI POSS�VEL ALTERAR CONTA. TENTE NOVAMENTE, POR FAVOR!</fonte></h1>
					<a href = 'lista-partidas.php?coligada=$coligada&lancamento=$partida&opcao=$opcao&lote=$lote'>Voltar</a>
	";
}

$tpl->show ();

?>