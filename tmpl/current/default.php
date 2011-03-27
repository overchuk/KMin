<?
	KM::ns('page');
	KM::ns('html');
	KM::ns('tmpl');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=KMpage::title();?></title>
<meta name="keywords" content="<?=KMpage::keywords();?>">
<meta name="description" content="<?=KMpage::description();?>">
<?=KMhtml::head();?>
</head>
<?=KMtmpl::$_body;?>
</html>
