<?

class KMmodule
{

	static public $names   = array();
	static public $classes = array();
	static public $tree    = array();

	static private $_cls = array();

	function clsid($name)
	{
		if(count(self::$_cls) != count(self::$classes))
			self::$_cls = array_combine(array_values(self::$classes), array_keys(self::$classes));

		return self::$_cls[ $name ];
	}


}


?>
