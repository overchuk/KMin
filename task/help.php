<?
	include '../config.php';
	KM::ns('lang');
	KMlang::seti( $_GET['lang'] );

	$s = urldecode($_GET['help']);
	if((strpos($s, '.') === false) && ($s[0] != '/'))
		$f = '../docs/help/'.KMlang::lang().'/'.$s.'.php';
	else
		$f = '';

	if(is_file($f))
		include $f;
	else
		echo '<span style="color:red; font-weight:bold;">'.MSG_HELP_NOT_FOUND.'</span>';

?>
