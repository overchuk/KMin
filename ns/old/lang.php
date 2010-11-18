<?
	// Русский язык

	class KMlang
	{
		static public $lang_names = array('рус');
		static public $langs = array('ru');
		static public $lang  = 'ru';
		static public $idx   = 0;
		static public $sep   = '<!-- | -->';

		function multi()
		{
			return (count(self::$langs) > 1);
		}

		function values($v)
		{
			return explode(self::$sep, $v);
		}

		function values_map($v)
		{
			$ret = '';
			$vs = self::values($v);
			$i = 0;
			foreach(self::$langs as $l)
				$ret[ $l ] = $vs[$i++];

			return $ret;
		}

		function val($s)
		{
			$ret = self::values($s);
		  	return $ret[ self::$idx ];	
		}

		/*
			Value for SU output

		 */
		function su($s, $max=0)
		{
			$ret = self::values($s);
			$r = trim(strip_tags($ret[0]));

			if($max  && (strlen($r) > $max))
			{
				if($max < 4)
					return mb_substr($r, $max);
				else
					return mb_substr($r, $max-3).'...';
			}

			if($r == '')
				$r = '<i>no value</i>';
		
			return $r;
		}

		function from($arr, $prefix)
		{
			$ret = array();
			foreach(self::$langs as $l)
				$ret[] = $arr[ $prefix.$l ];

			return implode(self::$sep, $ret);
		}

		function prepost()
		{
			if(count(self::$langs > 0))
			{
				$f = self::$langs[0];
				$c = strlen($f);
				$add = array();

				foreach($_POST as $n => $v)
					if(substr($n, -($c+1)) == '_'.$f)
					{
						$prefix = substr($n, 0, -($c+1));
						$add[$prefix] = self::from($_POST, $prefix.'_');
					}

				foreach($add as $n => $v)
					$_POST[$n] = $v;
			}
		}

	}
?>
