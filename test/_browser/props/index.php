<?
	include '../../../config.php';
	KM::ns('tmpl');
	KM::ns('props');
	KM::ns('class');
	
	
	
	$ps = array(

		'name' => array(
				'title' => 'Название',
				'descr' => 'Полное наименование продукта',
				'type' => array(
							'class' => 'tstring',
							'data' => array(),
						),
			),

		'price' => array(
				'title' => 'Цена',
				'descr' => '',
				'type' => array(
							'class' => 'tint',
							'data' => array('min' => 1),
					),
			),

		'type' => array(
				'title' => 'Тип',
				'descr' => '',
				'type' => array(
							'class' => 'tenum',
							'data' => array('values' => array(
													0 => 'Нету',
													1 => 'Первый тип',
													2 => 'Второй тип',
												))
					),
			),

		'art' => array(
				'title' => 'Артикул',
				'descr' => 'xxx-000: три символа и три числа через тире',
				'type' => array(
							'class' => 'tstring',
							'data' => array('mask' => '...-\d\d\d')
					),
			),
	);


	$row = array();

	KMtmpl::body_start();

	if( count($_POST) > 0)
	{
		echo '<pre>';
		var_dump($_POST);
		echo '</pre><hr>'.LF;

		$row = KMprops::form2value($ps);
		echo '<pre>';
		var_dump($row);
		echo '</pre><hr>'.LF;
	}

	//echo '<form method="POST" id="f1" onsubmit="return f1_on_submit();">'.LF;
	echo '<form method="POST" id="f1">'.LF;
	KMprops::ps2form('f1', $ps, $row);
	echo '<input type="submit" value="'.MSG_SUBMIT.'" />'.LF;
	echo '<form>'.LF;

	KMtmpl::body_end();
?>
