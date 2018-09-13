<?php

session_start ();

include_once 'validacao.php';

include_once 'global.php';

$tpl = new Template ( 'sped-excluir-fiscal-processa.html' );

$tpl->USUARIO = $_SESSION ['usuario'];

$usuario = $_SESSION ['usuario'];
$msg = 'teste';

extract ( $_REQUEST );
$msg = '';

$sql = "
DECLARE @identificador INT = '$identificador';
DECLARE @coligada INT = '$coligada';
DECLARE @idlaf INT = (SELECT IDLAF FROM DLAF WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador);

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

UPDATE TMOV SET CODLAFE = NULL WHERE CODCOLIGADA = @coligada AND IDMOV = @identificador;
";
$resultado = $db->Execute ( $sql );
//$affected = $db->Affected_Rows ();
//printvardie($affected);
if ($resultado) {
	$msg = "<font color = 'green'>Lançamento Fiscal (Liber) excluído com sucesso.</font>";
	$tpl->VOLTAR = "<button onclick='goBack2()'>Voltar</button>";
}else {
	$msg = "<font color = 'red'>Não foi possível excluir Lançamento Fiscal (Liber)!</font>";
	$tpl->VOLTAR = "<button onclick='goBack()'>Voltar</button>";
}

$tpl->MSG = $msg;

$tpl->show ();

?>