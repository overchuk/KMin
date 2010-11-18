<?

	class KMurl
	{
		// Configuration part
		static public $index;

		// Dynamic part
		static public $url;
		static public $path;
		static public $qs;
		static public $get;

		function init()
		{
			$index = 'index.php';
		}

		function parse($url = NULL, $reverse = false)
		{
			if(!isset($url))
			{
				$url = $_SERVER['REQUEST_URI'];
				$loc = false;
			}
			else
				$loc = true;

			list( self::$path, self::$qs) = explode('?', $url, 2);
			if($loc)
			{
				$ls = explode('&', self::$qs);
				foreach($ls as $l)
				{
					list($n, $v) = explode('=',$l,2); 
					self::$get[ urldecode($n) ] = urldecode($v);
				}

				if($reverse)
				{
					$_SERVER['REQUEST_URI'] = $url;
					$_GET = self::$get;
				}
			}
			else
				self::$get = $_GET;

		}
	}

?>
