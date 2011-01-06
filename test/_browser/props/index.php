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
				'value' => '',
			),

		'price' => array(
				'title' => '',
				'descr' => '',
				'type' => array(
							'class' => 'tint',
							'data' => array('min' => 1),
					),
				'value' => '100'	
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
				'value' => '1'	
			),

		'art' => array(
				'title' => 'Артикул',
				'descr' => 'xxx-000: три символа и три числа через тире',
				'type' => array(
							'class' => 'tstring',
							'data' => array('mask' => '...-\d\d\d')
					),
				'value' => 'xxx-000'
			),
	);



	KMtmpl::body_start();

	echo '<pre>';
	var_dump($_POST);
	echo '</pre><hr>'.LF;

	echo '<form method="POST" id="f1" onsubmit="return f1_on_submit();">'.LF;
	KMprops::ps2form('f1', $ps);
	echo '<input type="submit" value="'.MSG_SUBMIT.'" />'.LF;
	echo '<form>'.LF;

	KMtmpl::body_end();
?>
