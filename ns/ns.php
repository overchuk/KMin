<?

    class KM 
    {

		static $_const = array();

	function set($name, $value)
	{
		self::$_const[ $name ] = $value;
	}

	function _($name)
	{
		return self::$_const[ $name ];
	}

	function get($name)
	{
		return self::_($name);
	}




	function ns($class, $cfg = true)
	{
		if(!class_exists('KM'.$class))
			kmin_import($class, $cfg);
	}

	function type($class)
	{
		if(!class_exists('T_'.$class))
			include_once( DIR_TYPE.SL.$class.'.php' );
	}

    function cls($class, $data=array())
    {
        $cls = 'KM_'.$class;
        if(!class_exists($class))
            include_once( DIR_CLASS.SL.$class.'.php' );

        $c = new $cls;
        $c->init( $data );
        return $c;
    }

	function cmdline($input)
	{
		$ret = array();
		$arr = explode('&', $input);
		foreach($arr as $s)
		{
			list($n, $v) = explode('=', $s);
			$ret[$n] = urldecode($v);
		}

		return $ret;
	}

	function js_const()
	{
		return 'kmin.const.web_root = "'.WEB_ROOT.'";'.LF
				.'kmin.const.web_site = "'.WEB_SITE.'";'.LF;
	}

    }

?>
