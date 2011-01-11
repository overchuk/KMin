<?
	include '../config.php';
	KM::ns('lang');
	KMlang::seti( $_GET['lang'] );

	echo '<pre>';
	var_dump($_GET);
	echo '</pre>';
?>
