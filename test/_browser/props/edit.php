<?
	include '../../../config.php';
	KM::ns('tmpl');
	KM::ns('props');
	KM::ns('class');
	

	KMtmpl::body_start();

	$ps = array();

	if( KMprops::edit('f1', $ps) )
	{
		var_dump($ps);
	}


	KMtmpl::body_end();
?>
