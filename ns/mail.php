<?

class KMmail
{
	function addr($e, $trim = true)
	{

		if($trim)
		{
			$e = explode(' ', trim($e));
			$e = $e[count($e)-1];
			if(($e[0] == '<') && (substr($e,-1) == '>'))
				$e = substr(substr($e,1),0, -1);
		}

		$e = strtolower($e);
		if(preg_match('/^[-_a-z0-9]+(\.[-_a-z0-9]*)*@[-_a-z0-9]+(\.[-_a-z0-9]*)*$/', $e))
			return $e;
		else
			return '';
	}

	function henc($s, $from = '')
	{
		if($from)
			$s = iconv($from, 'utf8', $s);

	    return '=?utf-8?B?' . base64_encode($s) . '?=';
	}

	function html($addr, $subj, $body)
	{
    	return mail($addr, self::henc($subj),  $body,
                "From: robot@{$_SERVER["HTTP_HOST"]}\nContent-Type: text/html; charset=utf-8\nContent-Transfer-Encoding: 8bit\n");
	}


}

?>
