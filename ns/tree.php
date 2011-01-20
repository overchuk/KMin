<?

KM::ns('db');

class KMtree
{

	// Prepare place for $n item from $rid 
	function _prepare($table, $rid, $n=2)
	{
		$rid = intval($rid);
		$n = intval($n);
		KMdb::query('UPDATE `'.KMdb::table($table).'` SET `lid`=`lid`+'.$n.' WHERE `lid` >= '.$rid);
		KMdb::query('UPDATE `'.KMdb::table($table).'` SET `rid`=`rid`+'.$n.' WHERE `rid` >= '.$rid);
	}

	// Delete place from $lid to $rid
	function _hlop($table, $lid, $rid)
	{
		$n = intval($rid - $lid +1);
		KMdb::query(KMdb::sql('UPDATE `'.KMdb::table($table).'` SET `lid`=`lid`-'.$n.' WHERE `lid` > '.$rid));
		KMdb::query(KMdb::sql('UPDATE `'.KMdb::table($table).'` SET `rid`=`rid`-'.$n.' WHERE `rid` > '.$rid));
		self::cache_clear();

	}

	// How many from lid to rid
	function _fromto($table, $lid, $rid)
	{
		return KMdb::res(KMdb::query('SELECT COUNT(*) FROM `'.KMdb::table($table).'` WHERE `lid` > '.intval($lid).' AND `rid` < '.intval($rid)));
	}


	// Insert entire row
	function insert($table, $row)
	{
		KMdb::lock($table);
		if($row['pid'] == 0)
		{
			$rid = intval(KMdb::res(KMdb::query('SELECT MAX(`rid`) FROM `'.KMdb::table($table).'`')));
			$row['lid'] = $rid+1;
			$row['rid'] = $rid+2;
		}
		else
		{
			$p = KMdb::get($table, $row['pid']);
			self::_prepare($table, $p['rid'], 2);
			$row['lid'] = $p['rid'];
			$row['rid'] = $p['rid']+1;
		}
		KMdb::insert($table, $row);
		$ret = KMdb::id();
		KMdb::unlock($table);

		return $ret;
	}


	// Calculate number of sub pages
	function subcount($table, $id)
	{
		$r = KMdb::get($id);
		return self::fromto($table, $r['lid'], $r['rid']); 
	}

	// Erase page with subpages
	function erase($table, $id)
	{
		$r = self::get($id);
		KMdb::query('DELETE FROM `'.KMdb::table($table).'` WHERE `lid` >= '.intval($r['lid']).' AND `rid` <= '.intval($r['rid']));
		self::_hlop($table, $r['lid'], $r['rid']);
	}

	// Move row $r to childs of $pid
	function move($table, $id, $pid)
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
			self::_prepare($table, $p['rid'], $n);

			// Renew row (can be changed, by self::_prepare)
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
			$rid = intval(KMdb::get(KMdb::query(KMdb::sql('SELECT MAX(`rid`) FROM `'.KMdb::table($table).'`'))));
			$dn = '+'.($rid - $r['lid'] +1);
		}

		// Move all 
		KMdb::query(KMdb::sql('UPDATE `'.KMdb::table($table).'` SET `lid` = `lid`'.$dn.' WHERE `lid` >= '.$r['lid'].' AND `lid` < '.$r['rid']));
		KMdb::query(KMdb::sql('UPDATE `'.KMdb::table($table).'` SET `rid` = `rid`'.$dn.' WHERE `rid` > '.$r['lid'].' AND `rid` <= '.$r['rid']));

		// Change pid
		KMdb::query(KMdb::sql_update('page', array('pid' => $pid), ' WHERE `id`='.$r['id']));

		// Hlop old interval
		self::hlop($r['lid'], $r['rid']);
		

		KMdb::unlock();
	}

}

?>
