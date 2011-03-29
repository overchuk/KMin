<?

class KMutil
{

// Is $name can be identifier. (lat letters and numbers)
function isid($name)
{
    return preg_match('/^[a-z]+[a-z0-9_]*$/', $name);
}


// Parse command line, like URL GET request
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

// Update arrat $one by $two. 
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


// Find  url in arrat $items that located near $url.
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

// Generate uniq hex string. 
// Default length is $n=32
// if length > 31, $use_time - flag, to use time_t as part of string
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

// Get number with leading zero. Default length $l=6
function num($n, $l=6)
{
    $s = ''.intval($n).'';
    while(strlen($s) < $l)
        $s = "0$s";
    return $s;
}


// Fill %param% in $mask by values of array $row
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

// Parse mysql date, to array
function dat($d)
{
	list($year, $month, $day) = explode('-', $d);

	return array('year' => intval($year), 'month' => intval($month), 'day' => intval($day));
}
 
}

?>
