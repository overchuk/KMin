<?

KM::ns('hook');
KM::ns('log');

class KMpage
{
	// Cache of pages. Site row by id
	static $_pages = array();

	// Cache of sites. SiteId by host 
	static $_sites = array();

	// Cache of loaded path: chain of pages
	static $_path = array();

	// Property of page	
	static $_props  = array();


	function id()
	{
		if(!($c = count(self::$_path)))
			return 0;
		return self::$_path[$c-1]['id'];
	}	


	// Load set of properties from additional table
	function load_set($set, $table=null)
	{
		if($table == null)
			$table = $set;

		self::$_props['_'.$set] = KMdb::getw($table, '`pid`='.self::id());
	}
	
	function get_prop($name, $set="")
	{
		if($set)
			return self::$_props[$set][$name];
		else
			return self::$_props[$name];
	}

	function set_prop($name, $value, $set='')
	{
		if($set)
			self::$_props[$set][$name] = $value;
		else
			self::$_props[$name] = $value;
	}

	function _($name)
	{
		list($s,$n) = explode('.', $name, 2);
		if($n)
			return self::$_props['_'.$s][$n];
		else
			return self::$_props[$s];
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
		// XXX
		echo '<div style="width:100%; border:3px solid red;text-align:center;"><br><br>CONTENT<br><br></div>';
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
	function get($id, $alarm = true)
	{
		if(!isset(self::$_pages[ $id ]))
		{
			if($p = KMdb::get('pages', $id))
				self::$_pages[ $id ] = $p;
			else
			{
				if($alarm)
					KMlog::alarm('Pages', "Page [$id] not found.");
				else
					return false;
			}
		}

		return self::$_pages[ $id ];
	}



/*
	Get site row (row of site's main page) by host (default current HTTP host without www.)
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
			{
				// URL used as HTTP HOST for pid=0. Origin url is ''
				$s['url'] = '';
				self::$_sites[ $host ] = $s;
			}
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
		echo KMdb::sql('SELECT * FROM `#__pages` WHERE (`pid`='.intval($pid).') 
								AND (`url`="'.KMdb::val($url).'") AND '.self::where().' LIMIT 0,1');

		$res = KMdb::query('SELECT * FROM `#__pages` WHERE (`pid`='.intval($pid).') 
								AND (`url`="'.KMdb::val($url).'") AND '.self::where().' LIMIT 0,1');

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
	Clear loaded path (page chain)
*/	
	function _clear()
	{
		self::$_path = array();
		KMhook::hook('page:clear');
	}


/*
	Added page $row to path
	Standard way: Added to path, raise page:load
*/
	function _cd($row)
	{
		self::$_path[] = $r;
		KMhook::hook('page:load', $r);
	}

/*
	Load page chain, by  URL
	Fill $_path cache
	raise "page:load" by each loaded page
*/
	function load($url=null, $sid = null)
	{
		if(!$sid)
			$sid = self::sid();
		if(!$url)
			$url = strtolower( $_SERVER['REQUEST_URI'] );

		// First step 
		self::_clear();
		self::_cd($sid);

		// Initial hack
		$pid = $sid['id'];

		$us = explode('/', $url);
		foreach($us as $u)
		{
			$u = trim($u);
			if(!$u)
				continue;
			
			$r = self::sub($pid, $u, false);
			if(!$r)
				KMhttp::error('404');
			
			self::_cd($r);
			$pid = $r['id'];
		}
	}


/*
	Load page by ID (or row)
	Fill path as full way to $p
	if $p is array - it is row, id otherwise.
*/
	function load_byid($p, $alarm = true)
	{
		if(!is_array($p))
			$p = KMdb::get('pages', intval($p));

		if(!is_array($p))
		{
			if($alarm)
			{
				KM::ns('log');
				KMlog::alarm('page', 'Load page ['.intval($p).'] failed.', array($p));
			}
			
			return false;
		}

		
		self::_clear();
		$res = KMtree::path('pages', $p);
		
	}


/*
	XPath notation, for js/tree.js 
	i<id0>/i<id1>/...
	$id can be ID or ROW (array)
*/
	function xpath($id)
	{
		if(!is_array($id))
		{
			$id = self::get($id);
			if(!is_array($id))
				return '';
		}

		$lid = intval($id['lid']);
		$rid = intval($id['rid']);
		$id = intval($id['id']);

		$ret = array();
		$res = KMdb::query('SELECT `id` FROM `#__pages` WHERE `lid`<='.intval($lid).' AND `rid`>='.$rid.' ORDER BY `lid`');
		while($r = KMdb::fetch($res))
			$ret[] = 'i'.$r['id'];

		return implode('/', $ret);
	}


/*
	Quick number of subpages
*/
	function nsub($page)
	{
		return intval( ($page['rid'] - $page['lid'])/2 );
	}

/*
	Load all root elementa (all sites)
*/
	function query_sites()
	{
		return KMdb::query('SELECT * FROM `#__pages` WHERE (`pid`=0) ORDER BY `lid`');
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
		$where = array('pid = '.intval($id));

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
		
		return KMdb::query('SELECT * FROM `#__pages` WHERE ('.implode(') AND (', $where).') ORDER BY `lid`');
	}



/*
	Insert new page
*/
	function insert($pid, $row)
	{
		// XXX Need checks

		KM::ns('tree');
		$row['pid'] = $pid;
		$row['id']  = KMtree::insert('pages', $row);
		KMhook::hook('page:insert', $row);	
		return $row['id'];
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
		$row = self::get($id);
		if(!$row)
			return false;

		KM::ns('tree');
		KM::ns('hook');

		$ids = array($row['id']);
		$res = KMdb::query('SELECT `id` FROM `#__pages` WHERE `lid`>'.$row['lid'].' AND `rid`<'.$row['rid']);
		while($r = KMdb::fetch($res))	
			$ids[] = $r['id'];
		KMhook::hook('page:remove', $ids);

		KMdb::query('DELETE FROM `#__pages` WHERE `lid`>='.$row['lid'].' AND `rid`<='.$row['rid']);
		KMtree::_hlop('pages', $row['lid'], $row['rid']);
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
