<?
    KM::ns('class');
    KM::ns('util');
    KM::ns('log');


    class KM_
    {
        /*
            All classes has one data array
        */
        var $data = array(); 

	function name()
	{
		return $this->data['kmin']['class'];
	}

        function _html($tmpl='', $part='', $cls='')
        {
            if(!$cls)
                $cls = $this->data['tmpl']['class']; 
            if(!$cls)
                $cls = $this->data['kmin']['class'];
            
            if(!$part)
                $part = $this->data['tmpl']['part'];
            if($part)
                $part .= SL;

            if(!$tmpl)
                $tmpl = $this->data['tmpl']['name'];
            if(!$tmpl)
                $tmpl = 'default';

            $f = DIR_TEMPLATE.SL.$cls.SL.$part.$tmpl.'.php';
            if(is_file($f))
            {
                ob_start();
                include($f);
                $ret = ob_get_contents();
                ob_end_clean();
                return $ret;
            }
            else
                KMlog::alarm('Template', 'tmpl-file not found:'.$f);

            return '';
        }

        function _show($tmpl='', $part='', $cls='')
        {
            echo $this->_html($tmpl, $part, $cls);    
        }

        // Internal init
        function _init($def, $in = null)
        {
            if(!isset($in))
                $in = array();
            elseif(!is_array($in))
                $in = unserialize($in);
 
            KMutil::update($def,$in);
            $this->data = $def;
        }

        // Virtual init
        function init($in = null)
        {
            if(isset($in))
            {
                if(is_array($in))
                    $this->data = $in;
                else
                    $this->data = unserialize($in);
            }
        }

    }

?>
