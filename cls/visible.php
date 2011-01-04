<?
    KM::ns('class');

    /*
      Root class for displayed classes. 
    */
    class KM_visible extends KM_
    {


	/*
          Get HTML code for display this object
          Incapsulate in separate template files
        */
        function html($tmpl='', $part='', $cls='')
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

        function show($tmpl='', $part='', $cls='')
        {
            echo $this->html($tmpl, $part, $cls);    
        }
    }

?>
