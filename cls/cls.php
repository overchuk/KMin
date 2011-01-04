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
