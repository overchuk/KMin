<?
	include '../../../config.php';
	KM::ns('lang');
	KMlang::set('ru');

	KM::ns('tmpl');
	KM::ns('props');
	KM::ns('class');
	


	KMtmpl::body_start();

	$ps = array();

	if( KMprops::edit('f1', $ps) )
	{
		echo '<pre>';
		var_dump($ps);
		echo '</pre>'.LF;
	}


	KMtmpl::body_end();
?>
