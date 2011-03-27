<?
    KM::ns('tmpl');
    KM::ns('html');
	header("content-type: text/html; charset=utf-8");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=$title;?></title>
<meta name="keywords" content="<?=$keywords;?>">
<meta name="description" content="<?=$description;?>">
<?=KMhtml::head();?>
</head>
<?=KMtmpl::$_body;?>
</html>
