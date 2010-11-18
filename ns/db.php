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

	function vals($s)
	{
		$ret = array();
		foreach($s as $n => $v)
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
		if(! @mysql_connect(self::$host, self::$user, self::$pass))
		{
			$error = mysql_error();
			KMlog::alarm('SLQ', 'open = '.$error);
			return false;
		}

		mysql_select_db(self::$base);
		mysql_query('SET NAMES UTF8');

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


	// Prepare DB array, use unchecked int array, and unchecked str array
    function db($db_int, $db_str)
    {
        $ret = array();
        foreach($db_int as $n => $v)
            $ret[ $n ] = intval( $v );
    
        foreach($db_str as $n => $v)
            $ret[ $n ] = self::val( $v );
   
        return $ret; 
    }

	function sql($q, $tmpl = '#__')
	{
		return str_replace($tmpl, self::$prefix, $q);
	}

	function query($q, $alarm = TRUE)
	{
		$res = mysql_query($q);
		if((!$res) AND $alarm)
		{
			$error = mysql_error();
			KMlog::alarm('SQL', $q.' : '.$error);
		}
		return $res;
	}

	function sql_query($q, $alarm = TRUE)
	{
		return self::query(self::sql($q), $alarm);
	}

	function fetch($res)
	{
		if($res)
			return mysql_fetch_assoc($res);
		else
			return false;
	}

	function col($res, $n=0)
	{
		if(self::num($res))
			return mysql_result($res,0,$n);
		else
			return false;
	}

    function num($res)
    {
        return mysql_num_rows($res);
    }

	function insert_id()
	{
		return mysql_insert_id();
	}

	function table($name)
	{
		return self::$prefix . $name;
	}




    // INSERT AND UPDATE ================================================

    function sql_insert($table, $fields)
    {
        $keys = array_keys($fields);
        for($i=0; $i<count($keys); $i++)
            $keys[$i] = '`'.$keys[$i].'`';
        $keys = implode(',', $keys);

        $vals = array_values($fields);
        for($i=0; $i<count($vals); $i++)
        {
            if(is_array($vals[$i]))
                $v = $vals[$i]['raw'];
            else
                $v = '"'.$vals[$i].'"';

            $vals[$i] = $v;
        }

        $vals = implode(',', $vals);

        return 'INSERT INTO `'.self::table($table).'` ('.$keys.') VALUES ('.$vals.')';
    }

    function insert($table, $fields, $alarm = true)
    {
        return self::query( self::sql_insert($table, $fields), $alarm );
    }


    function sql_update($table, $fields, $where = "")
    {
        $tmp = array();
        foreach($fields as $n => $a)
        {
            if(is_array($a))
                $v = $a['raw'];      
            elseif($a[0] == '"')
                $v = substr($a,1);
            else
                $v = '"'.$a.'"';

            $tmp[] = '`'.$n.'` = '.$v;
        }

        $val = implode(', ', $tmp);

		if($where)
			$where = ' WHERE '.$where;

        return self::sql('UPDATE `'.self::table($table).'` SET '.$val.' '.$where);
    }

    function update($table, $fields, $where = "")
    {
        return self::query( self::sql_update($table, $fields, $where) );
    }

	function sql_kupdate($table, $fields, $key, $val)
	{
		return self::sql_update($table, $fields, ' `'.$key.'` = "'.$val.'"');
	}

    function kupdate($table, $fields, $key, $val)
    {
        return self::query( self::sql_kupdate($table, $fields, $key, $val) );
    }

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
    // ========================================================================




    // GET DATA  ===============================================

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


    // Get master-slave on-to-one model
    // Slave row is included as sub-array named '_'
    function get2($t1, $t2, $v, $k2='pid', $k1='id')
    {
        $ret = self::get($t1, $v, $k1);
        $ret['_'] = self::get($t2, $v, $k2);
        return $ret;
    }

    // ========================================================================



    // Deleting ================

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
    // =========================



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

        $res = KMdb::sql_query('SELECT `'.$orde.'` FROM `#__'.$table.'` WHERE '.$w.' ORDER BY `'.$orde.'` DESC LIMIT 0,1');
        if(KMdb::num($res) == 0)
            return false;

        $v = KMdb::col($res);

        KMdb::update($table, array($orde => $r[$orde]), ' `'.$orde.'`='.$v);
        KMdb::kupdate($table, array($orde => $v), $key, $id);

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

        $res = KMdb::sql_query('SELECT `'.$orde.'` FROM `#__'.$table.'` WHERE '.$w.' ORDER BY `'.$orde.'` ASC LIMIT 0,1');
        if(KMdb::num($res) == 0)
            return false;

        $v = KMdb::col($res);
        KMdb::update($table, array($orde => $r[$orde]), ' `'.$orde.'`='.$v);
        KMdb::kupdate($table, array($orde => $v), $key, $id);

        return true;
    }

}
?>
