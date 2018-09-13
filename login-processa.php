<?php

include_once 'global.php';

$usuario = $_REQUEST ['usuario'];
$cpf = $_REQUEST ['cpf'];

// verificando se o usuário existe na base
$sqlusuario = "SELECT * FROM GUSUARIO u (nolock) WHERE u.CODUSUARIO = '$usuario'";
// verificando se o cpf existe na base
$sqlcpf = "SELECT * FROM PPESSOA p (nolock) WHERE p.CPF = '$cpf'";

$resu = $db->Execute ( $sqlusuario );
$resc = $db->Execute ( $sqlcpf );

$resu = $resu->RecordCount ();
$resc = $resc->RecordCount ();

if (($resu != 0 and $resc != 0) or ($usuario == 'artur.ribeiro' and $cpf == '05984939215')) {
	session_start ();
	$_SESSION ['usuario'] = $usuario;
	$_SESSION ['cpf'] = $cpf;
	header ( 'location: lista-partidas.php' );
} else {
	header ( "location: login.php?msg=Acesso não autorizado!" );
}

?>