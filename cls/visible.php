<?
    KM::ns('class');

    /*
      Root class for displayed classes. 
    */
    class KM_visible extends KM_
    {


	/*
          Show HTML code for display this object
          Incapsulate in separate template files
        */

        function show($tmpl='', $part='', $cls='')
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
                include($f);
            }
            else
			{
				KM::ns('log');
                KMlog::alarm('Template', 'tmpl-file not found:'.$f);
			}
        }

        function html($tmpl='', $part='', $cls='')
        {
			ob_start();
			self::show($tmpl, $part, $cls);
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
        }

    }

?>
