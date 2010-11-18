<?

KM::ns('util');

class KMipc
{

	static $_pid     = null;
	static $_timeout = 300; 


	function pid()
	{
		if(!isset(self::$_pid))
			self::$_pid = KMutil::uniq(8,false);

		return self::$_pid;
	}

	function lock($name)
	{
		$res = KMdb::update('mutex', array('tout' => (time() + self::$_timeout), 'pid' => self::pid()), ' WHERE `name`="'.KMdb::val($name).'" AND `tout` < '.time());
		return (mysql_affected_rows() > 0);
	}

	function unlock($name)
	{
		KMdb::update('mutex', array('tout' => 0, 'pid'=>'' ), ' WHERE `name`="'.KMdb::val($name).'" AND `pid`="'.self::pid().'"');
	}
	
}

?>
