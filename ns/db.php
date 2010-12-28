<?

KM::ns('log');
KM::ns('http');


class KMdb
{
	static public $host;
	static public $user;
	static public $pass;
	static public $base;
	static public $prefix;


	function val($s)
	{
		return mysql_escape_string($s);
	}

	function sval($s)
	{
		return '"'.self::val($s).'"';
	}

	function vals($s, $i = array())
	{
		$ret = array();
		foreach($s as $n => $v)
			$ret[ $n ] = self::sval($v);
		foreach($i as $n => $v)
			$ret[ $n ] = self::val($v);

		return $ret;
	}

    function set($a)
    {
        if(!is_array($a))
            $a = array($a);

        $ns = array();
        foreach($a as $n)
            $ns[] = intval($n);

		if(count($ns) == 0)
			return '(0)';
        return '('.implode(',',$ns).')';
    }

	function open()
	{
		if(!@mysql_connect(self::$host, self::$user, self::$pass))
		{
			$error = mysql_error();
			KMlog::alarm('SLQ', 'open = '.$error);
			return false;
		}

		if(!@mysql_select_db(self::$base))
		{
			KMlog::alarm('SQL', 'Can`t select db: "'.self::$base.'", '.mysql_error());
			return false;
		}

		if(!@mysql_query('SET NAMES UTF8'))
		{	
			KMlog::alarm('SQL', 'setup UTF8 failed: '.mysql_error());
			return false;
		}

		return true;
	}

	function table($name)
	{
		return self::$prefix . $name;
	}

	function sql($q, $tmpl = '#__')
	{
		return str_replace($tmpl, self::$prefix, $q);
	}

	
	function query($q, $alarm = TRUE)
	{
		$q = self::sql($q);
		$res = mysql_query($q);
		if((!$res) AND $alarm)
		{
			$error = mysql_error();
			KMlog::alarm('SQL', $q.' : '.$error);
		}
		return $res;
	}

	function fetch($res)
	{
		if($res)
			return mysql_fetch_assoc($res);
		else
			return false;
	}

	function load($res)
	{
		$ret = array();
		while($r = self::fetch($res))
			$ret[] = $r;

		return $ret;
	}

	function kload($res, $key = 'id')
	{
		$ret = array();
		while($r = self::fetch($res))
			$ret[ $r[$key] ] = $r;

		return $ret;
	}

	function id()
	{
		return mysql_insert_id();
	}
	
    function num($res)
    {
        return mysql_num_rows($res);
    }

	function res($res, $n=0)
	{
		if(self::num($res))
			return mysql_result($res,0,$n);
		else
			return false;
	}


    function getw($table, $where)
    {
        $table = self::table($table);
        $res = self::query('SELECT * FROM `'.$table."` WHERE $where".' LIMIT 0,1');
        if($res)
            return self::fetch($res);
        else
            return null;  
    }

    function get($table, $val, $key='id')
    {
        $val = self::val($val);
        return self::getw($table, '`'.$key.'` = "'.$val.'"');
    }

    function delw($table, $w)
    {
        $table = self::table($table);
        return self::query('DELETE FROM `'.$table.'` WHERE '.$w);
    }

    function del($table, $id, $key='id')
    {
        if(is_array($id))
            return self::delw($table, '`'.$key.'` IN '.self::set($id));
        else
            return self::delw($table, '`'.$key.'` = '.intval($id));
    }
	


    function sql_insert($table, $fields)
    {
        return 'INSERT INTO `'.self::table($table).'` (`'.implode('`,`', array_keys($fields)).'`) VALUES ('.implode(',', array_values($fields)).')';
    }


    function sql_update($table, $fields, $where = "")
    {
        $tmp = array();
        foreach($fields as $n => $a)
            $tmp[] = '`'.$n.'` = '.$v;
        $val = implode(', ', $tmp);
		if($where)
			$where = ' WHERE '.$where;
        return self::sql('UPDATE `'.self::table($table).'` SET '.$val.' '.$where);
    }


	function sql_kupdate($table, $fields, $val, $key='id')
	{
		return self::sql_update($table, $fields, ' `'.$key.'` = "'.$val.'"');
	}


    function insert($table, $fields, $alarm = true)
    {
        return self::query( self::sql_insert($table, $fields), $alarm );
    }

    function update($table, $fields, $where = "", $alarm = true)
    {
        return self::query( self::sql_update($table, $fields, $where), $alarm );
    }

    function kupdate($table, $fields, $val, $key='id', $alarm = true)
    {
        return self::query( self::sql_kupdate($table, $fields, $val, $key), $alarm );
    }


// ====== Algorithms
	// Force: INSERT OR UPDATE
	function forcew($table, $db, $where)
	{
		$t = self::table($table);

		$res = self::query('SELECT `id` FROM `'.$t.'` WHERE '.$where.'  LIMIT 0,1');
		if(mysql_num_rows($res) < 1)
			self::query(self::sql_insert($table, $db));
		else
		{
			$id = mysql_result($res,0,0);
			self::query(self::sql_kupdate($table, $db, 'id', $id));
		}
	}

	function force($table, $db, $name, $value)
	{	
		$db[ $name ]  = $value;
		self::forcew($table, $db, '`'.$name.'` = "'.$value.'"');
	}


	// Up or down in order list 
    function orde_up($table, $id, $opt = array())
    {
		KM::ns('util');

		$def = array('orde' => 'orde', 
						'key' => 'id', 
						'where' => '');

		KMutil::update($def, $opt);	
		$key = $def['key'];
		$orde = $def['orde'];
		$where = $def['where'];

        $r = self::get($table, $id, $key);
    
        $w = '`'.$orde.'`<'.$r[$orde];
        if($where)
            $w = "($w) AND ($where)";

        $res = KMdb::query('SELECT `'.$orde.'` FROM `#__'.$table.'` WHERE '.$w.' ORDER BY `'.$orde.'` DESC LIMIT 0,1');
        if(KMdb::num($res) == 0)
            return false;

        $v = KMdb::res($res);

        KMdb::update($table, array($orde => $r[$orde]), ' `'.$orde.'`='.$v);
        KMdb::kupdate($table, array($orde => $v), $id, $key);

        return true;
    }

    function orde_down($table, $id, $opt = array())
    {
		KM::ns('util');

		$def = array('orde' => 'orde', 
						'key' => 'id', 
						'where' => '');

		KMutil::update($def, $opt);	
		$key = $def['key'];
		$orde = $def['orde'];
		$where = $def['where'];

        $r = self::get($table, $id, $key);

        $w = '`'.$orde.'`>'.$r[$orde];
        if($where)
            $w = "($w) AND ($where)";

        $res = KMdb::query('SELECT `'.$orde.'` FROM `#__'.$table.'` WHERE '.$w.' ORDER BY `'.$orde.'` ASC LIMIT 0,1');
        if(KMdb::num($res) == 0)
            return false;

        $v = KMdb::res($res);
        KMdb::update($table, array($orde => $r[$orde]), ' `'.$orde.'`='.$v);
        KMdb::kupdate($table, array($orde => $v), $key, $id);

        return true;
    }


	function lock()
	{
		// XXX
		// Need locking, for race fixes
	}

	function unlock()
	{
		// XXX
	}
}

?>
