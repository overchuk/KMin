<?

KM::ns('hook');
KM::ns('log');

class KMpage
{

	static $_ps     = array();
	static $_curr   = array();
	static $_props  = array();
	static $_url    = '';
	static $_id     = null;
	static $_data   = null;
	static $_static = null;

	static $_b = null;


	function _where($t = '')
	{
		// XXX
		if($t)
			$t = "$t.";
		return " ($t`archive` = 0) ";
	}

	function get($id)
	{
		if(!isset(self::$_ps[$id]))
			self::$_ps[$id] = KMdb::get('pages', $id);

		return self::$_ps[ $id ];
	}

	function curr($name = null)
	{
		$c = count(self::$_curr);
		if($c)
		{
			if(isset($name))
				return self::$_curr[ $c-1 ][ $name ];
			else
				return self::$_curr[ $c-1 ];
		}
		else
			return false;
	}

	function id()
	{
		if(!isset(self::$_id))
			self::$_id = self::$_curr[ count(self::$_curr)-1 ]['id'];

		return self::$_id;
	}

	function url()
	{
		return self::$_url;
	}

	function host()
	{
		if(count(self::$_curr) > 0)
			return self::$_curr[0]['id'];
		else
		{
			KM::ns('http');
			$r = self::_load(0, KMhttp::host());
			return $r['id'];
		}
	}

	function type()
	{
		$c = count(self::$_curr);
		return self::$_curr[$c-1]['type'];
	}


	/*

		Get pid of static page by module name ($mod)
		By default ($alarm = true) raise alarm,
		if static page not found

	*/
	function thepid($mod, $alarm = true)
	{
		KM::ns('module');	

		if(!is_array(self::$_static))
		{
			$res = KMdb::sql_query('SELECT `id`,`type` FROM `#__pages` WHERE `static`=1 AND '.self::_where());
			while($r = KMdb::fetch($res))
				self::$_static[ KMmodule::mod($r['type']) ] = $r['id'];
		}

		$ret = intval(self::$_static[ $mod ]);
		if(($ret == 0) && $alarm)
			KMlog::alarm('pages', 'Static not found for: '.$mod, array( self::$_static ));

		return $ret;
	}

	function update_prop($name, $value)
	{
		if($value)
		{
			KM::ns('util');
			$p = KMutil::format($value, self::$_props);
			self::$_props[ $name ] = $p;
		}
	}

	function get_prop($name)
	{
		return self::$_props[$name];
	}

	function set_prop($name, $value)
	{
		self::$_props[$name] = $value;
	}

	function title()
	{
		return self::get_prop('title');
	}

	function description()
	{
		return self::get_prop('description');
	}

	function keywords()
	{
		return self::get_prop('keywords');
	}

	function content()
	{
		return self::get_prop('content');
	}

	function _load($pid, $url)
	{
		if($pid == 0)
			$res = KMdb::sql_query('SELECT * FROM `#__pages` WHERE `pid`='.$pid.' AND `mirror` LIKE "%'.KMdb::val($url).'|%" AND `archive`=0 LIMIT 0,1');
		else
			$res = KMdb::sql_query('SELECT * FROM `#__pages` WHERE `pid`='.$pid.' AND `url` = "'.KMdb::val($url).'" AND `archive`=0 LIMIT 0,1');

		return KMdb::fetch($res);
	}

	function load($url)
	{
		$c = self::curr();
		if($c)
			$pid = $c['id'];
		else
			$pid = 0;

		$r = self::_load($pid, $url);
		if(!is_array($r))
			KMhttp::error(404);

		if($pid)
			self::$_url .= '/'.$url;

		self::$_ps[ $r['id'] ] = $r;

		self::$_curr[] = $r;
		self::update_prop('title',		 KMlang::val($r['title'])       );
		self::update_prop('description', KMlang::val($r['description']) );
		self::update_prop('keywords',    KMlang::val($r['keywords'])    );

		KMhook::hook('page:load', $r);
	}

	function complete()
	{
		/*
		self::$_data = KMdb::get('page_data', self::id(), 'pid');
		self::set_prop('name',    KMlang::val( self::$_data['name'] ));
		self::set_prop('h1',      KMlang::val( self::$_data['h1'] ));
		*/
		$p = self::curr();
		self::set_prop('name',    KMlang::val( $p['name'] ));
		self::set_prop('h1',      KMlang::val( $p['h1']   ));

		KMhook::hook('page:complete', $p);
	}

	function get_url($pid, $lang=null)
	{
		KM::ns('lang');
	
		$u = '';
		$id = $pid;
		$h = self::host();
		while($id != $h)
		{
			$p = self::get($id);
			if($p['pid'] == 0)
				KMlog::alarm('Page', 'Full url failed for '.$pid);

			if($u)
				$u = $p['url'].SL.$u;
			else
				$u = $p['url'];

			$id = $p['pid'];
		}

		return KMlang::url($lang).SL.$u;
	}

	
	function set_block($text, $name, $param='')
	{
		self::$_b[$name][$param] = $text;
	}

	function block($name, $param = '')
	{
		if(!isset(self::$_b[$name][$param]))
		{
			$f = DIR_BLOCKS.SL.$name.'.php';
			if(!is_file($f))
				self::$_b[$name][$param] = '<div class="error missing-block">'.$f.'<br>'.$name.'</div>'.LF;
			else
			{
				ob_start();
				$var = str_replace('.', '_', $name);
				$$var = $param;
				include $f;
				self::$_b[$name][$param] = ob_get_contents();
				ob_end_clean();
			}
		}	

		return self::$_b[$name][$param];
	}


}

?>
