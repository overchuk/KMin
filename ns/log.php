<?

KM::ns('mail');

class KMlog
{

	static public $admin_mail        = '';
    static public $show_errors       = true; 
    static public $continue_on_error = false;

	function init()
	{}

    function dump($a)
    {
        ob_start();
        var_dump($a);
        $ret = ob_get_contents();
        ob_end_clean();

        return $ret;
    }   

	function alarm_str($s)
	{
		if(self::$admin_mail)
			KMmail::html(self::$admin_mail, 'ALARM from '.HOST, $s);

        if(self::$show_errors)
            echo $s;
            
        if(!self::$continue_on_error)
            die();
	}
	
	function alarm($module, $message, $objects = NULL)
	{
		if($module)
			$d = '<h1>ALARM in module: <font color="red">'.$module.'</font></h1>'.LF;
		else
			$d = '<h1>ALARM</h1>'.LF;

		$d .= '<strong>'.$message.'</strong><hr>'.LF;

		if(isset($objects))
		{
			if(!is_array($objects))
				$objects = array($objects);

			$d .= '<h2>OBJECTS</h2>'.LF;

			foreach($objects as $b)
			{
				ob_start();
				var_dump($b);
				$d .= nl2br(ob_get_contents()).'<hr>'.LF;
				ob_end_clean();
			}
		}

		$arr = debug_backtrace();

		foreach($arr as $t)
		{
			$s = '';
			foreach($t['args'] as $a)
				$s .= ', '.(is_object($a) ? 'OBJ' : $a);
			$s = substr($s, 2);
			$d .= 'vim '.$t['file'].' +'.$t['line'].' - <font color="gray" size="-2">'.$t['function']."($s)</font><br>".LF;
		}

	    ob_start();
    	echo '<hr>DATE: '.date('Y-m-d H:i').'<hr>'.LF;
	    var_dump($_SERVER);
    	echo '<hr>'.LF;
	    var_dump($_POST);
    	echo '<hr>'.LF;
	    $d .= ob_get_contents();
    	ob_end_clean();

		self::alarm_str($d);
	}	
}

?>
