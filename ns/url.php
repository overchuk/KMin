<?

class KMurl
{

    static $_req = '';
    static $_query   = '';

    function val($v)
    {
        return urlencode($v);
    }

    function unval($v)
    {
        return urldecode($v);
    }

    function _parse()
    {
        list(self::$_req, self::$_query) = explode('?', $_SERVER['REQUEST_URI']);
        self::$_query = urldecode(self::$_query);

        self::$_req = substr(self::$_req, strlen(WEB_SITE));
        if(substr(self::$_req, -1) == '/')
            self::$_req = substr(self::$_req, 0, -1);
    }

    function req()
    {
        if(!$_req)
            self::_parse();

        return self::$_req;
    }

    function query()
    {
        if(!$_req)
            self::_parse();

        return self::$_query;
    }




    function url($one, $two='')
    {
        if(!is_array($one))
            $one = array($one => $two);

        $u = array();
        foreach($one as $n=>$v)
            $u[] = "$n=".self::val($v); 

        return '?'.implode('&',$u);
    }

    function set($one, $two='')
    {
        if(!is_array($one))
            $one = array($one => $two);

        $u = array();
        foreach($_GET as $n => $v)
            if(isset($one[$n]))
            {
                $u[] = "$n=".self::val($one[$n]);
                unset($one[$n]);
            }
            else
                $u[] = "$n=".self::val($v); 

        foreach($one as $n=>$v)
            $u[] = "$n=".self::val($v); 

        return '?'.implode('&',$u);
    }


    function clear($one)
    {
        if(!is_array($one))
            $one = array($one);

        $u = array();
        foreach($_GET as $n => $v)
            if(!in_array($n, $one))
                $u[] = "$n=".self::val($v); 

        return '?'.implode('&',$u);
    } 

    function cs($clear, $set)
    {
        if(!is_array($clear))
            $clear = array();

        if(!is_array($set))
            $set = array();

        foreach($_GET as $n => $v)
            if(!isset($set[$n]))
                $set[$n] = $v;

        $u = array();
        foreach($set as $n => $v)
            if(!in_array($n, $clear))
                $u[] = "$n=".self::val($v);

        return '?'.implode('&', $u);
    }


    function create($v, $row)
    {
        if(!is_array($v))
            return KMutil::format($v, $row);
       
        $c = $v['clear'];
        if(is_array($c))
            foreach(array_keys($c) as $i)
                $c[$i] = KMutil::format($c[$i], $row);

        $s = $v['set'];
        if(is_array($s))
            foreach(array_keys($s) as $i)
                $s[$i] = KMutil::format($s[$i], $row);

        return self::cs($c,$s); 
    }

	function my()
	{
		return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	function href()
	{
		return base64_encode( self::my() );
	}
}

?>
