<?
	include '../../config.php';
	KM::ns('lang');
	KMlang::seti( $_GET['lang'] );

	KM::ns('class');
	$t = KMclass::create($_GET['type']);
	echo $t->admin_form($_GET['prefix']);
?>
