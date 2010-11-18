<?

class KMmodule
{

static $_modules	= array();
static $_names		= array();
static $_tree		= array();
static $_templates	= array();
static $_revers		= null;

function _reverse()
{
	self::$_revers = array_combine( array_values(self::$_modules), array_keys(self::$_modules) );
}

function mod($id = null)
{
	if(!isset($id))
		$id = KMpage::id();

	return self::$_modules[ $id ];
}

function lst($type = null)
{
	if(!isset($type))
		return self::$_names;

	$ret = array();
	foreach( self::$_tree[ $type ] as $t )
		$ret[ $t ] = self::$_names[ $t ];

	return $ret;
}

function name($i)
{
	return self::$_names[ $i ];
}

function type($name)
{
	if(!is_array(self::$_revers))
		self::_reverse();

	return intval(self::$_revers[ $name ]);
}

function tmpl($mod, $tid)
{
	$a = self::$_templates[$mod];
	if(!is_array($a))
		return $a;

	KM::ns('tmpl');

	$t = KMtmpl::get($tid);
	if(in_array($t, $a))
		return $t;
	else
		return $a[0];
}

function template()
{
	KM::ns('page');
	KM::ns('tmpl');
	$p = KMpage::curr();
	$ret = self::tmpl(intval($p['type']), intval($p['tmpl']));
	if(!$ret)
		$ret = KMtmpl::get(0);

	return $ret;
}

function load()
{
	KM::ns('page');
	$p = KMpage::curr();
	$f = DIR_MODULE.'/'.self::$_modules[ KMpage::type() ].'.php';

	ob_start();
	if(is_file($f))
		include $f;
	else
	{
		$f2 = DIR_MODULE.'/simple.php';
		if(is_file($f2))
			include $f2;
		else
		{
			KM::ns('log');
			KMlog::alarm('mods', 'No modules includes', array($f, $f2));
		}
	}
	KMpage::set_prop('content', ob_get_contents());
	ob_end_clean();
}

}

?>
