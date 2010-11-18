<?
	class KMconst
	{
        static $_     = array();
		static $const = array();	
	
		
		function val($type, $name)
		{
			if(is_array(self::$const[$type]))
				return self::$const[$type][$name];
		
			ns('sql');
			ns('lang');

			$res = KMsql::query(sql('SELECT * FROM `#__param` WHERE `type`="'.KMsql::val($type).'"'));
			while($r = KMsql::fetch($res))
				self::$const[$type][ $r['name'] ] = KMlang::val($r['value']);

			return self::$const[$type][$name];
		}
	}

?>
