<?

ns('html');

include_once DIR_TYPE.SL.'type.php';

class KMproperty
{
	static public  $types = array();
	static private $_types = array();

	/*
	 *
	 *  Show form rows for each field
	 *   $fid - HTML form id, 
	 *   $ps  - Array of item descriptions
	 *   $vs  - Array of item values
	 *
	 */
	function form($fid, $ps, $vs=array())
	{
		foreach($ps as $i => $v)
		{
			$title = $v['title'];
			$descr = $v['descr'];
			$type  = $v['type'];
			$param = $v['param'];
			$p = self::type($type, $param);			
			$input = $p->input($i, $fid);

			echo KMhtml::tr(
						KMhtml::th($title.'<br><font size="-2">'.$descr.'</font>').
						KMhtml::td($input)
				);
		}
	}


	function vals($fid, $ps, $vs=null)
	{
		$err  = array();
		$vals = array();
				
		if(!is_array($vs))
			$vs = $_POST;

		foreach($ps as $i => $v)
		{
			$p = self::type($v['type'], $v['param']);
			$r = $p->val( $vs[$i] );
			if($r === false)
				$err[$i] = $p->error(); 
			$vals[] = $r; 
		}

		return array('values' => $vals, 'errors' => $err);
	}

	function type($type, $cmdline)
	{
		$p = self::$_types[$type];
		if(!isset($p))
		{
			if(!in_array($type, self::$types))
			{
				KMlog::alarm('KMproperty', "type '$type' undefined", array(self::$types, $type, $cmdline));
				return false;
			}

			include_once DIR_TYPE.SL.$type.'.php';
			$c = 'T_'.$type;
			$p = new $c();
			self::$_types[$type] = $p;
		}

		return $p->from_cmdline($v);
	}
}


?>
