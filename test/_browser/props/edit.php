<?
	include '../../../config.php';
	KM::ns('lang');
	KMlang::set('ru');

	KM::ns('tmpl');
	KM::ns('props');
	KM::ns('class');
	


	KMtmpl::body_start();

	$ps = array();
	$ret = array();

	if( KMprops::edit('f1', $ps, $ret) )
	{
		echo '<pre>';
		var_dump($ret);
		echo '</pre>'.LF;
	}


	KMtmpl::body_end();
?>
