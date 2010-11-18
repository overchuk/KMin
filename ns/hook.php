<?
class KMhook
{
	static $_cls   = array();
	static $_hooks = array();

	
	function call($cls,$func,&$opt)
	{
		$c = self::$_cls[ $cls ];
		if(!is_object($c))
		{
			$c = new $cls;
			self::$_cls[ $cls ] = $c;
		}

		return $c->$func( $opt );
	}



	function set($name, $cls, $func)
	{
		self::$_hooks[$name][] = array($cls, $func); 
	}

	function _clear($name, $cls='', $func='')
	{
		if($cls == '')
			self::$_hooks[$name] = array();
		else
		{
			$hs = array();
			foreach(self::$_hooks[$name] as $h)
				if(($cls != $h[0]) || ($func && ($func != $h[1])))
					$hs[] = $h;
			self::$_hooks[$name] = $hs;
		}
	}

	function clear($name = '', $cls = '', $func = '')
	{
		if($name)
			self::_clear($name, $cls, $func);
		else
		{
			if(!$cls)
				self::$_hooks = array();
			else
			{
				$ns = array_keys(self::$_hooks);
				foreach($ns as $n)
					self::_clear($n, $cls, $func);
			}	
		}
	}

	function hook($name, &$opt = array())
	{
		$hs = self::$_hooks[$name];
		if(is_array($hs))
			foreach($hs as $h)
				self::call($h[0], $h[1], $opt);
	}

	

}
?>
