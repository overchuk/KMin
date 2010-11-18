<?

class KMsession
{
	
	function start()
	{
		session_start();
	}

	function restart()
	{
		$_SESSION = array();
		session_destroy();	
		session_start();	
	}
}

?>
