<?
	
	
class KMparam
{

static $_val = null;

function _load()
{
	KM::ns('db');
	$res = KMdb::sql_query('SELECT * FROM `#__params`');
	while($r = KMdb::fetch($res))
	{
		if($r['type'] < 20)
			self::$_val[ $r['name'] ] = KMlang::val( $r['value'] );
		else
			self::$_val[ $r['name'] ] = $r['value'];
	}
}

function get($name)
{
	if(!is_array(self::$_val))
		self::_load();

	return self::$_val[$name];
}

}

?>
