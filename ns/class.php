<?
class KMclass
{

function _uses($cls='', $dir = DIR_CLASS)
{
    $name = 'KM_'.$cls;
    if(!class_exists($name))
    {
        if(!$cls)
            $cls = 'cls';

        $f = $dir.SL.$cls.'.php';
        if(!is_file($f))
            KMlog::alarm('CLASS', 'missing class '.$cls);

        include($f);
    }
    return $name;
}

function uses($cls = '')
{
    return self::_uses($cls);
}

function user($cls = '')
{
    return self::_uses($cls, DIR_USER_CLASS);
}

function create($cls, $data = null)
{
    $name = self::uses($cls);
    $ret = new $name;

	if(!isset($data))
		$data = array();
	if(!is_array($data))
		$data = unserialize($data);

    $data['kmin']['class'] = $cls;
    $ret->init($data);
    return $ret;
}

// Standard 
function obj(&$in, $store = false, $inplace=false)
{
    if(is_object($in))
        return $in;

    if(is_object($in['obj']))
        return $in['obj'];

    $obj = self::create($in['class'], $in['data']);
    if($store)
    {
        if($inplace)
            $in = $obj;
        else
            $in['obj'] = $obj;
    }
    return $obj;
}

function unobj($in)
{
    if(is_object($in))
    {
        if( $in->data && $in->data['kmin'] && $in->data['kmin']['class'] )
            return array('class' => $this->data['class'], 'data' => self::unobj($in->data));
        else
            KMlog::alarm('Class', 'Can`t UnObj', array($in));
    }
    
    if( !is_array($in) )
        return $in;

    $ks = array_keys($in);
    $ret = array();
    foreach($ks as $k)
        $ret[ $k ] = self::unobj( $in[$k] );

    return $ret;
}

function toString($c)
{
    return serialize( self::unobj($c) );
}

function formString($s)
{
    $obj = self::obj(unserialize($s));
    return self::create($obj['class'], $obj['data']);
}


}

KMclass::uses();

?>
