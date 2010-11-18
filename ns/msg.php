<?

class KMmsg
{

    static $lang = '';
    static $set  = '';

    function init()
    {
        $d = DIR_MSG.SL;
        if(self::$lang)
            $d .= self::$lang.SL;

        foreach(self::$set as $s)
            include_once($d.$s.'.php');
    }

    function val($mask, $params = array())
    {
        if(count($params) > 0)
        {
            $fr = array();
            $to = array();

            foreach($params as $n => $v)
            {
                $fr[] = '$'.$n;
                $to[] = $v;

                $mask = str_replace($fr, $to, $mask);
            }
        }

        return $mask;
    }
}

?>
