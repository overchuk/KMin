<?

KM::ns('page');
KM::ns('param');

/*
function _b($name, $param=0)
{
	echo KMpage::block($name, $param);
}

function _c($name)
{
	echo KMparam::get($name);
}

function _t($name)
{
	KMtmpl::show($name);
}

function _p($name)
{
	echo KMpage::get_prop( $name );
}
*/


class KMtmpl
{
	static $_templates;
	static $_revers     = null;

	function _reverse()
	{
		if(!isset($_revers))
			$_revers = array_combine( array_values( self::$_templates ), array_keys( self::$_templates ));
	}

	function get($id)
	{
		return self::$_templates[ $id ];
	}

	function id($name)
	{
		self::_reverse();
		return $_revers[ $name ];
	}	

    function show($t = 'default' )
    {
        include DIR_TEMPLATE.SL.$t.'.php';
    }

    function obj($obj, $type, $template = '')
    {
        // Subset
        $t = $obj->data['templates'][$type];
        if($t)
            $t .= SL;

        $data = $obj->data[$type];
        if($template == '')
        {
            $f = DIR_TEMPLATE.SL.$t.str_replace('.', SL, $type).'.php';
            if(!is_file($f))
                $f = DIR_TEMPLATE.SL.$t.str_replace('.', SL, $type).SL.'default.php';
        }
        else
            $f = DIR_TEMPLATE.SL.$t.str_replace('.', SL, $type).SL.$template.'.php';
           
        include($f);
    }

    function data($data, $type, $template = '')
    {
        self::obj( KM::cls('cls', $data), $type, $template );
    }

    function body_start($body_attr = '')
    {
        if($body_attr)
            $body_attr = " $body_attr";
        ob_start();
        echo "<body $body_attr>\n";
    }

    function body_begin($s = '')
    {
        return self::body_start($s);
    }

    function body_end($template='default', $category='')
    {
        echo LF.'</body>';
        $body = ob_get_contents();
        ob_end_clean();

        if($category)
            $category = $category.'/';

        include DIR_TEMPLATE.SL.$category.$template.'.php';
    }

/*
	function draw(&$this, $cat, $tmpl='default')
	{
		@include DIR_TEMPLATE.SL.$cat.SL.$tmpl.'.php';
	}
*/

}



?>
