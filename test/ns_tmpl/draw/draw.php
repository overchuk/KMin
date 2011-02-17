<?
	error_reporting (E_ALL & ~E_NOTICE);
    include '../../../config.php';
    kmin_import('ns');

	KM::ns('tmpl');

	$data['title'] = 'Very good';
	KMtmpl::draw($data, 'test', 'a1');
?>
