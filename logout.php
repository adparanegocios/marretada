<?php
session_start ();
session_destroy ();
$_SESSION ['usuario'] = array ();
$_SESSION ['cpf'] = array ();
header ( 'location: login.php' );
?>