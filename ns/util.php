<?

class KMutil
{

function cmdline($input)
{
    $ret = array();
    $arr = explode('&', $input);
    foreach($arr as $s)
    {
        list($n, $v) = explode('=', $s);
        $ret[$n] = urldecode($v);
    }

    return $ret;
}

function update(&$one, &$two)
{
    if(!is_array($two))
        return;

    foreach($two as $n => $v)
        if(is_array($one[$n]) AND is_array($v))
            self::update($one[$n], $v);
        else
            $one[$n] = $v;
}

function suburl($url, $items)
{
    $ret = '';
    foreach($items as $i)
    {
        if(strlen($i) > strlen($ret))
            if($i == substr($url,0,strlen($i)))
                $ret = $i;
    }

    return $ret;
}

function uniq($n = 32, $use_time = true)
{
    $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'a', 'b', 'c', 'd', 'e', 'f');

    if(($n > 31) && $use_time)
    {
        $ret = dechex(time());
        $n -= strlen($ret);
    }
    else
        $ret = '';

    for($i = 0; $i<$n; $i++)
        $ret .= $hex[array_rand($hex, 1)];

    return $ret;
}

function num($n, $l=6)
{
    $s = ''.intval($n).'';
    while(strlen($s) < $l)
        $s = "0$s";
    return $s;
}

function format($mask, $row)
{
    $ar = explode('%', $mask);
    $i = 0;
    $ret = '';
    foreach($ar as $s)
    {
        $i = 1-$i;
        if($i)
            $ret .= $s;
        elseif($s == '')
            $ret .= '%';
        elseif($s[0] == '~')
            $ret .= KMlang::val($row[ substr($s,1) ]);
        else
            $ret .= $row[$s];
    }

    return $ret;
}

function dat($d)
{
	list($year, $month, $day) = explode('-', $d);

	return array('year' => intval($year), 'month' => intval($month), 'day' => intval($day));
}
 
}

?>
