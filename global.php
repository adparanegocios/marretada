<?php

ini_set ( 'display_errors', 0 );
error_reporting ( 0 );

include_once 'classes/class.Util.php';
include_once 'classes/Template.class.php';

define ( 'HOST_BANCO', '10.10.15.66' );
define ( 'USER_BANCO', 'sa' );
define ( 'PASS_BANCO', 'RBA13#' );
define ( 'NAME_BANCO', 'CORPORERM' );

define ( 'HOST_EMAIL', 'mail.gruporba.com.br' );
define ( 'USER_EMAIL', 'rm@rbadecomunicacao.com.br' );
define ( 'PASS_EMAIL', 'RBA13#' );
define ( 'PORT_EMAIL', '25' );
define ( 'PERCENTAGEM_LIMITE', '80' );

$vColigadas = array(
						1 => 'DIÁRIO DO PARÁ', 
						2 => 'RBA TV', 
						3 => 'DIÁRIO FM', 
						4 => '99 FM',
						5 => 'RÁDIO CLUBE DO PARÁ A PODEROSA',
						6 => 'SISTEMA CLUBE DO PARÁ DE COMUNICAÇÃO',
						7 => 'SNC SISTEMA NORTE DE COMUNICAÇÃO',
						8 => 'DOL - INTERMEDIAÇÃO DE NEGÓCIOS E PORTAL DE INTERNET LTDA'
					);

include_once 'conexao.php';

if (end ( explode ( "/", $_SERVER ['PHP_SELF'] ) ) != 'login.php') {
	include_once 'menu.php';
}

?>