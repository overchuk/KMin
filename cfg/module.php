<?

	KMmodule::$_modules = array(
					0   => 'static',
					99  => 'main',
					1   => 'news.list',
					11  => 'news',
					2 => 'feedback',
		            21 => 'form',
					3 => 'sitemap',
					4 => 'subscr',
					5 => 'catalog',
					55 => 'product',
		);

	KMmodule::$_names = array(
				0  => 'Текстовая страница',
				99 => 'Главная страница',
				1  => 'Список новостей',
				11 => 'Новость',
				2  => 'Обратная связь',
				21 => 'Форма обратной связи',
				3  => 'Карта сайта',
				4  => 'Подписка',
				5 => 'Каталог',
				55 => 'Товар',
			);

	KMmodule::$_tree = array(
				99 => array (0,1,2,3,5),
				0  => array(0,2),
				1  => array(11,1,0),
				3 => array(0),
				4 => array(0),
				5  => array(55,5,0),
				11 => array(0),
			);

	KMmodule::$_templates = array(
				99 => array('main', 'index'),
				0  => array('index'),
				1  => array('index'),
				2  => array('index'),
				21  => array('index'),
				11 => array('index'),
				3  => array('index'),
				4  => array('index'),
			);


?>
