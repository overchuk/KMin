<?
	setlocale(LC_ALL, "ru_RU.UTF8", "rus_RUS.UTF8");
    ini_set('allow_call_time_pass_reference', 'On');

	function kmin_def($name, $value)
	{
		if(!defined($name))
			define($name, $value);

		return constant($name);
	}

	/*  Platform depended */
	kmin_def('SL', '/');
	kmin_def('LF', "\n");
	kmin_def('HOST', $_SERVER['HTTP_HOST']);


	/* Where is KMin */
	kmin_def('DIR_ROOT', dirname(__FILE__));

	/* Site configuration */
	kmin_def('WEB_SITE', '');
	kmin_def('DIR_SITE',       dirname(DIR_ROOT));

	/* Kmin configuration */
	kmin_def('WEB_ROOT',   WEB_SITE.'/kmin');
	kmin_def('WEB_ADMIN',  WEB_SITE.'/admin');
	kmin_def('WEB_CSS',    WEB_ROOT.'/css' );
	kmin_def('WEB_JS',     WEB_ROOT.'/js'  );
	kmin_def('WEB_IMG',    WEB_ROOT.'/img' );
	kmin_def('WEB_ICON',   WEB_ROOT.'/icon');
	kmin_def('WEB_IMAGES', WEB_ROOT.'/images');
	kmin_def('WEB_FILES',  WEB_ROOT.'/files');


	kmin_def('DIR_USER',       DIR_ROOT .'/static');
	kmin_def('DIR_BLOCKS',     DIR_ROOT .'/blocks');
	kmin_def('DIR_MODULE',     DIR_ROOT .'/modules');
	kmin_def('DIR_TEMPLATE',   DIR_ROOT . SL . 'tmpl/current');
	kmin_def('DIR_NS',         DIR_ROOT . SL . 'ns');
	kmin_def('DIR_CFG',        DIR_ROOT . SL . 'cfg');
	kmin_def('DIR_CLASS',      DIR_ROOT . SL . 'cls');
	kmin_def('DIR_TYPE',       DIR_ROOT . SL . 'type');
	kmin_def('DIR_TASK',       DIR_ROOT . SL . 'task');
	kmin_def('DIR_MSG',        DIR_ROOT . SL . 'msg');
	kmin_def('DIR_ERROR',      DIR_ROOT . SL . 'err');
	kmin_def('DIR_TASK',       DIR_ROOT . SL . 'task');



    /* Initial functions */
	function kmin_import($class, $cfg = true)
	{
		$f = DIR_NS.SL.$class.'.php';
		require_once($f);

		if($cfg)
		{
			$f = kmin_def('CFG_'.$class, DIR_CFG.SL.$class.'.php');
			if(is_file($f))
				include_once($f);
		}
	}

    // Include common namespace
    kmin_import('ns');
?>
