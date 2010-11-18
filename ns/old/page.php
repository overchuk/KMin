<?

ns('db');
ns('log');

class KMPage
{

	// ============ PAGES FUNCTIONALLITY ============================
	// Pages over  sql-tree

	function insert($pid, $type, $name)
	{
		$id = self::insert_row( array('pid' => intval($pid), 'type' => intval($type), 'name' => $name) );
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `url`="'.$id.'", `orde`='.($id*100).', date="'.date('Y-m-d').'"'));
	}


	// ============ SQL TREE FUNCTIONALLITY =============================
	// replace '#__page'  to any other pages, and it will be work too

	static $cache = array();

	function get($id)
	{
		if(!isset(self::$cache[$id]))
			self::$cache[$id] = KMdb::fetch(KMdb::query(KMdb::sql('SELECT * FROM `#__page` WHERE `id`='.$id)));
		return self::$cache[$id];
	}

	function quick_furl($id)
	{
		if($id == 1)
			return '/';

		if(isset(self::$cache[$id]['furl']))
			return self::$cache[$id]['furl'];

		$ret = '/';
		while( isset(self::$cache[$id]) )
		{
			if($id == 1)
				return $ret;

			$ret = '/'.self::$cache[$id]['url'].$ret; 
			$id = self::$cache[$id]['pid'];
		}
		return false;
	}

	function furl($id)
	{
		$ret = self::quick_furl($id);
		if($ret)
			return $ret;

		$r = self::get($id);
		if(!is_array($r))
		{
				KMlog::alarm('FURL of missied page: '.$id);
			return '';
		}

		$res = KMdb::query(KMdb::sql('SELECT * FROM `#__page` WHERE `lid`<'.intval($r['lid']).' AND `rid`>'.intval($r['rid']).' ORDER BY `lid`'));
		$url = '';
		while($row = KMdb::fetch($res))
		{
			$url .= $row['url'].'/';
			$row['furl'] = $url;
			self::$cache[$row['id']] = $row;
		}

		return $url;
	}

	function cache_clear()
	{
		self::$cache = array();
	}

	// Prepare place for $n item from $rid 
	function prepare($rid, $n=2)
	{
		$rid = intval($rid);
		$n = intval($n);

		KMdb::query(KMdb::sql('UPDATE `#__page` SET `lid`=`lid`+'.$n.' WHERE `lid` >= '.$rid));
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `rid`=`rid`+'.$n.' WHERE `rid` >= '.$rid));
		self::cache_clear();
	}

	// Delete place from $lid to $rid
	function hlop($lid, $rid)
	{
		$n = intval($rid - $lid +1);
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `lid`=`lid`-'.$n.' WHERE `lid` > '.$rid));
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `rid`=`rid`-'.$n.' WHERE `rid` > '.$rid));
		self::cache_clear();

	}

	// Insert entire row
	function insert_row($row)
	{
		$db = array();
		foreach($row as $n => $v)
			$db[$n] = KMdb::val($v);

		$pid = intval($row['pid']);

		KMdb::lock();
		if($pid == 0)
		{
			$rid = intval(KMdb::get(KMdb::query(KMdb::sql('SELECT MAX(`rid`) FROM `#__page`'))));
			$db['lid'] = $rid+1;
			$db['rid'] = $rid+2;
		}
		else
		{
			$p = self::get($row['pid']);
			self::prepare($p['rid'], 2);
			$db['lid'] = $p['rid'];
			$db['rid'] = $p['rid']+1;
		}
		KMdb::query(KMdb::sql_insert('page', $db));
		KMdb::unlock();		

		return KMdb::insert_id();
	}


	function fromto($lid, $rid)
	{
		return KMdb::get(KMdb::query(KMdb::sql('SELECT COUNT(*) FROM `#__page` WHERE `lid` > '.intval($lid).' AND `rid` < '.intval($rid))));
	}

	// Calculate number of sub pages
	function subcount($id)
	{
		$r = self::get($id);
		return self::fromto($r['lid'], $r['rid']); 
	}


	// Erase page with subpages
	function erase($id)
	{
		$r = self::get($id);
		KMdb::query(KMdb::sql('DELETE FROM `#__page` WHERE `lid` >= '.intval($r['lid']).' AND `rid` <= '.intval($r['rid'])));
		self::hlop($r['lid'], $r['rid']);
	}

	// Move row $r to childs of $pid
	function move($id, $pid)
	{
		KMdb::lock();

		// Get item
		$r = self::get($id);
		if(!is_array($r))
			return false;


		if($pid > 0)
		{
			// Get new parent
			$p = self::get($pid);
			if(!is_array($p))
				return false;

			// New parent already my child
			if(($p['lid'] > $r['lid']) && ($p['lid'] < $r['rid']))
				return false;

			// Interval length
			$n = intval($r['rid'] - $r['lid'] + 1);

			// Prepare place in new parent
			self::prepare($p['rid'], $n);

			// Renew row (can be changed, by self::prepare)
			$r = self::get($r['id']);

			// Calculate dN and prepare as SQL statement
			$dn = $p['rid'] - $r['lid'];
			if($dn < 0)
				$dn = '-'.abs($dn);
			else
				$dn = '+'.$dn;
		}
		else
		{
			$rid = intval(KMdb::get(KMdb::query(KMdb::sql('SELECT MAX(`rid`) FROM `#__page`'))));
			$dn = '+'.($rid - $r['lid'] +1);
		}

		// Move all 
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `lid` = `lid`'.$dn.' WHERE `lid` >= '.$r['lid'].' AND `lid` < '.$r['rid']));
		KMdb::query(KMdb::sql('UPDATE `#__page` SET `rid` = `rid`'.$dn.' WHERE `rid` > '.$r['lid'].' AND `rid` <= '.$r['rid']));

		// Change pid
		KMdb::query(KMdb::sql_update('page', array('pid' => $pid), ' WHERE `id`='.$r['id']));

		// Hlop old interval
		self::hlop($r['lid'], $r['rid']);
		

		KMdb::unlock();
	}

	function path($id)
	{
		$r = self::get($id);
		if(!is_array($r))
			return false;

		$ret = array();
		$res = KMdb::sql_query('SELECT * FROM `#__page` WHERE `lid`<'.$r['lid'].' AND `rid`>'.$r['rid'].' ORDER BY `lid`');
		while($row = KMdb::fetch($res))
			$ret[] = $row;
		
		$ret[] = $r;
		return $ret;
	}
}
?>
