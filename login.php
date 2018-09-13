<?php

include_once 'global.php';

$tpl = new Template ( 'login.html' );

if (isset ( $_REQUEST ['msg'] )) {
	$tpl->MSG = $_REQUEST ['msg'];
}

$tpl->show ();

?>