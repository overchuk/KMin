<?
	class KMuser
	{
		static public $uid;
		static public $user;

		function init()
		{
			self::$uid = intval($_SERVER['uid']);
			if(self::$uid > 0)
			{
				$q = KMdb::sql('SELECT `id`, `login`, `name`, `email` FROM `#__user` WHERE `id`='.self::$uid.' LIMIT 0,1');
				$user = mysql_fetch_assoc(KMdb::query($q));
		
				if(!$user)
					$uid = 0;
			}
			else
				$user = array();			
		}
	
		
	}
?>
