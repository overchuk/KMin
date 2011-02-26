<?
	KM::ns('page');
	KM::ns('html');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=KMpage::title();?></title>
<meta name="keywords" content="<?=KMpage::keywords();?>">
<meta name="description" content="<?=KMpage::description();?>">
<?=KMhtml::head();?>
</head>
<?=$body;?>
</html>
