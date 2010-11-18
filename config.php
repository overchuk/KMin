<?
	setlocale(LC_ALL, "ru_RU.UTF8", "rus_RUS.UTF8");
    ini_set('allow_call_time_pass_reference', 'On');

	define('DIR_ROOT', dirname(__FILE__));

	/* Site configuration */
	if(!defined('WEB_SITE'))
		define('WEB_SITE', '');

	if(!defined('WEB_ROOT'))
		define('WEB_ROOT', WEB_SITE.'/kmin');

    if(!defined('DIR_SITE'))
	    define('DIR_SITE', dirname(DIR_ROOT));

	if(!defined('DIR_USER'))
		define('DIR_USER', DIR_SITE.'/static');

	if(!defined('DIR_BLOCKS'))
		define('DIR_BLOCKS', DIR_SITE.'/blocks');

	if(!defined('DIR_MODULE'))
		define('DIR_MODULE', DIR_SITE.'/modules');

	if(!defined('WEB_ADMIN'))
		define('WEB_ADMIN',  WEB_SITE.'/admin');

	if(!defined('DIR_TEMPLATE'))
		define('DIR_TEMPLATE',   DIR_ROOT . SL . 'tmpl/current');

	/* Auto defined */
	define ('HOST', $_SERVER['HTTP_HOST']);

	/*  Platform depended */
	define('SL', '/');
	define('LF', "\n");

	define('WEB_CSS',    WEB_ROOT.'/css' );
	define('WEB_JS',     WEB_ROOT.'/js'  );
	define('WEB_IMG',    WEB_ROOT.'/img' );
	define('WEB_ICON',   WEB_ROOT.'/icon');
	define('WEB_IMAGES', WEB_ROOT.'/images');
	define('WEB_FILES',  WEB_ROOT.'/files');


	define('DIR_NS',         DIR_ROOT . SL . 'ns');
	define('DIR_CFG',        DIR_ROOT . SL . 'cfg');
	define('DIR_CLASS',      DIR_ROOT . SL . 'cls');
	define('DIR_TYPE',       DIR_ROOT . SL . 'type');
	define('DIR_TASK',       DIR_ROOT . SL . 'task');
	define('DIR_MSG',        DIR_ROOT . SL . 'msg');
	define('DIR_ERROR',      DIR_ROOT . SL . 'err');
	define('DIR_TASK',       DIR_ROOT . SL . 'task');

    /* Initial functions */
	function kmin_import($class)
	{
		$f = DIR_NS.SL.$class.'.php';
		require_once($f);

		$f = DIR_CFG.SL.$class.'.php';
		if(is_file($f))
			include_once($f);
	}

    // Include common namespace
    kmin_import('ns');
?>
