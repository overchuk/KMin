<?
	include '../../config.php';
	KM::ns('tmpl');
	KMtmpl::body_start();

	echo '<h2>Properties</h2>'.LF;
	echo '<li><a href="props/form.php">Auto form</a></li>'.LF;
	echo '<li><a href="props/edit.php">Edit properties</a></li>'.LF;

	KMtmpl::body_end();
?>
