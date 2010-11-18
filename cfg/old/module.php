<?
	// Русский язык


	KMmodule::$names = array(
				0 => 'Текстовая страница',
				1 => 'Новости',
				2 => 'Новость',
				3 => 'Каталог',
				4 => 'Страна',
				5 => 'Курорт',
				6 => 'Область',
				7 => 'Отель',

				99 => 'Главная',
			);

	KMmodule::$classes = array(
				0 => 'simple',
				1 => 'news',
				2 => 'news.detail',
				3 => 'catalog',
				4 => 'catalog.country',
				5 => 'catalog.city',
				6 => 'catalog.area',
				7 => 'catalog.hotel',
				99 => 'main',
			);


	KMmodule::$tree = array(
				99 => array(0,1,3),
				0 => array(0),
				1 => array(2,0),
				3 => array(4,0),
				4 => array(5,0),
				5 => array(6,7,0),
				6 => array(7,0)
			);

/* links */
	
?>
