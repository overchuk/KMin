<?

KM::ns('hook');
KM::ns('log');

class KMpage
{
	// Cache of pages. Site row by id
	static $_pages = array();

	// Cache of sites. SiteId by host 
	static $_sites = array();

	// Cache of loaded path chain of pages
	static $_path = array();

	// Property of page	
	static $_props  = array();


	
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


/*
	XXX
	Need to implement language mask
*/
	function where()
	{
		return "( TRUE )";
	}

/*
	Get page by id.
	If page in cache, use it, otherwise read DB and store page in cache
*/
	function get($id)
	{
		if(!isset(self::$_pages[ $id ]))
		{
			if($p = KMdb::get('pages', $id))
				self::$_pages[ $id ] = $p;
			else
				KMlog::alarm('Pages', "Page [$id] not found.");
		}

		return self::$_pages[ $id ];
	}



/*
	Get site Id (id of site's main page) by host (default current HTTP host without www.)
	if alarm is true, raise alarm when site not found
*/
	function sid($host = null, $alarm = true)
	{
		if(!$host)
			$host = KMhttp::host();

		if(!isset(self::$_sites[ $host ]))
		{
			$s = KMdb::getw('pages', '(`pid` = 0) AND (`url` LIKE "%|'.KMdb::val($host).'|%")');
			if($s)
				self::$_sites[ $host ] = $s;
			elseif($alarm)
				KMlog::alarm('pages', 'Site ['.$host.'] not found');
		}

		return self::$_sites[ $host ];	
	}


/*
	Get sub page by URL
*/
	function sub($pid, $url, $alarm = true)
	{
		$res = KMdb::query('SELECT * FROM `#__pages` WHERE (`pid`='.intval($pid).') 
								AND (`url`="'.KMdb::val($url).'") AND '.KMdb::where().' LIMIT 0,1');

		if(! ($r = KMdb::fetch($res)))
		{
			if($alarm)
				KMdb::alarm('Page', 'Step ['.$p['id'].' => '.$u.'] not found');
			else
				return false;
		}

		self::$_pages[ $r['id'] ] = $r;
		return $r;
	}


/*
	Load page chain, by  URL
	Fill $_path cache
	raise "page:load" by each loaded page
	calculate properties by each page
*/

	function load($url, $sid = null)
	{
		if(!$sid)
			$sid = self::sid();

		self::$_path = array( $sid );

		$r = self::get($sid);
		if(!is_array($p))
			KMlog::alarm('page', "Site [$sid] not found");

		$us = explode('/', $url);
		foreach($us as $u)
		{
			$u = trim($u);
			if(!$u)
				continue;
			
			$r = self::sub($r['id'], $u, false);
			if(!$r)
				KMhttp::error('404');

			KMhook::hook('page:load', $r);
			if(KMmodule::is_last($r['type']))
			{
				KMmodule::load($r);
				break;
			}
		}
	}


/*
	Load childs of page
	options:
		'all'    - if isset, load all childes. Otherwise, only active
		'module' - type of array of types module to load
		'where'  - additional where statiment in sql query
		
	by default: no options
*/
	function query_childs($id, $opt = array())
	{
		$where = array();

		if(isset($opt['module']))
		{
			if(is_array($opt['module']))
				$where[] = '`type` IN '.KMdb::set($opt['module']);
			else
				$where[] = '`type` = "'.KMdb::val($opt['module']).'"';
		}
		
		if(!$opt['all'])
			$where[] = self::where();

		if(isset($opt['where']))
			$where[] = $opt['where'];
		
		if(count($where) == 0)
			$where = 'TRUE';		
	
		return KMdb::query('SELECT * FROM `#__pages` WHERE ('.implode(') AND (', $where).') ORDER BY `lid`');
	}



/*
	Insert new page
*/
	function insert($pid, $row)
	{
		// XXX
		
		KMhook::hook('page:insert', $row);	
	}


/*
	Move page
*/
	function move($id, $pid)
	{
		// XXX

		KMhook::hook('page:move', array($id, $pid));
	}
	

/*
	Delete page
*/
	function remove($id)
	{
		// XXX
		$row = self::get($id);
		if(!$row)
			return false;

		KMhook::hook('page:remove', $row);
	}



// ============================ OLD INTERFACE
/*


	static $_ps     = array();
	static $_curr   = array();
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
*/

}

?>
