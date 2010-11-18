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
			);

	KMmodule::$_tree = array(
				99 => array (0,1),
				0  => array(0),
				1  => array(11),
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
