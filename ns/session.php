<?

KM::ns('hook');

class KMsession
{

static $_time = 0;

function start()
{
	if(self::$_time)
		session_set_cookie_params(self::$_time);
	session_start();

	KMhook::hook('session:start');
}

function stop()
{
	KMhook::hook('session:stop');

	// Hack, true clear data, 
    $_SESSION = array();
	session_regenerate_id();
	session_destroy();
}

function restart()
{
	self::stop();
	self::start();
}


}

?>
