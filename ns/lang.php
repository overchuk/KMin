<?
	// Русский язык

	class KMlang
	{
		static public $lang_names = array('рус');
		static public $langs = array('ru');
		static public $lang  = 'ru';
		static public $idx   = 0;
		static public $sep   = '<!-- | -->';
		static public $need_lang = false;	
		static public $mask = array('R');

		function multi()
		{
			return (count(self::$langs) > 1);
		}

		function lang()
		{
			return self::$lang;
		}
	
		function idx()
		{
			return self::$idx;
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

                if(is_array($_POST))
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

        function js_init()
        {
            if(!(KMlang::multi()))
                return '';

            $v = array();
            foreach(KMlang::$lang_names as $l)
                $v[] = "'$l'";

            $ret = 'kmin.re.ls = new Array('.implode(', ', $v).');'.LF;

            $v = array();
            foreach(KMlang::$langs as $l)
                $v[] = "'input-$l'";

            $ret .= 'kmin.re.cs = new Array('.implode(', ', $v).');'.LF;

            return $ret;
        }

		
		function set($url)
		{
			$i = 0;
			
			foreach(self::$langs as $l)
			{
				if($l == $url)
				{
					self::$idx  = $i;
					self::$lang = $l;
					return true;
				}
			
				$i++;
			}

			if(self::$need_lang)
			{
				KM::ns('log');
				KMlog::alarm('Lang', 'No language url:'.$url);
			}
			return false;
		}

		function url($lang = null)
		{

			$idx = false;
			if(isset($lang))
				$idx = array_search($lang, self::$langs);
			if($idx === false)
				$idx = self::$idx;

			if(self::$need_lang || $idx)
				return WEB_SITE.SL.self::$langs[ $idx ];
			else
				return WEB_SITE;
		}

	}

?>
