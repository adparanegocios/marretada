<?php

require_once 'classes/adodb5/adodb.inc.php';

define ( 'HOST_BANCO', '10.10.15.66' );
define ( 'USER_BANCO', 'sa' );
define ( 'PASS_BANCO', 'RBA13#' );
define ( 'NAME_BANCO', 'CorporeRM' );

try {
	
	$db = & ADONewConnection ( 'odbc_mssql' );
	$dsn = "Driver={SQL Server};Server=" . HOST_BANCO . ";Database=" . NAME_BANCO . ";";
	$conexao = $db->Connect ( $dsn, USER_BANCO, PASS_BANCO );
	
	function &fetch_array(&$rs) {
		$numeroColunas = $rs->FieldCount ();
		while ( ! $rs->EOF ) {
			for($i = 0; $i < $numeroColunas; $i ++) {
				$coluna = $rs->FetchField ( $i );
				$nomeColuna = strtolower ( $coluna->name );
				$tipoColuna = $rs->MetaType ( $coluna->type );
				if ($tipoColuna == 'D') {
					$vetor ["$nomeColuna"] = $this->converteData ( $rs->fields [$i] );
				} else {
					$vetor ["$nomeColuna"] = $rs->fields [$i];
				}
			}
			$vetorRegistro [] = $vetor;
			$rs->MoveNext ();
		}
		return $vetorRegistro;
	}

} catch ( Exception $e ) {
	echo "<h1 align = 'center'>Banco de Dados indisponível. Tente novamente mais tarde, por favor.</h1>'";
	exit ();
}

extract ( $_REQUEST );
$cor = "";

$sql = "
SELECT 
	T.CODTMV AS [CODIGO],
	T.CODTMV +' => '+ UPPER(T.NOME) AS [MOVIMENTO]
FROM TTMV T (NOLOCK)
WHERE
	(T.CODCOLIGADA = $coligada) AND
	(T.NOME NOT IN ('XXXXXXXXXXXXXXXXXXXXXXX', '###################')) AND
	(T.CLASSIFICACAO IN ('04.08.02') OR T.CODTMV IN ('1.2.10', '1.2.31', '1.2.32', '1.2.34', '1.2.36'))
";

$rs = $db->execute ( $sql );

echo "<div id = 'movimentos'>";

if ($rs) {
	while ( $o = $rs->FetchNextObject () ) {
		
		$codigo = $o->CODIGO;
		$nome = $o->MOVIMENTO;
		
		$sql = "
			SELECT 
				T.EDICAONUMEROMOV AS [EDICAO],
				E.NFEESTADUAL AS [ATIVO]
			FROM TTMV T (NOLOCK)
			INNER JOIN TTMVEXT E (NOLOCK) ON (E.CODTMV = T.CODTMV AND E.CODCOLIGADA = T.CODCOLIGADA)
			WHERE
				T.CODCOLIGADA = $coligada AND
				T.CODTMV IN ('$codigo');
		";
		
		$aux = $db->execute ( $sql );
		
		while ( $a = $aux->FetchNextObject () ) {
			if ($a->EDICAO == 'E' and $a->ATIVO == 0) {
				$cor = "red";
			} else {
				$cor = "";
			}
		}
		$html = "<input name= 'movimentos[]' type='checkbox' value='$nome'> <font color = '$cor'> $nome </font> <br />";
		echo utf8_encode ( $html );
	}
}

echo "<div/>";

?>