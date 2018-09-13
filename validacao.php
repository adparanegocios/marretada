<?php
if (! (isset ( $_SESSION ['usuario'] ) and isset ( $_SESSION ['cpf'] ))) {
	header ( 'location: logout.php' );
	exit;
}
?>