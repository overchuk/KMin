<?
    KM::ns('log');

    class KMhttp
    {
        static $errors = array();  
        static $host = null;

        function redirect($url = null)
        {
            if(!isset($url))
                $url = $_SERVER['REQUEST_URI'];

            header('Location: '.$url);
            die();
        }


        function error($err)
        {
            if(in_array($err, self::$errors))
            {
                include DIR_ERROR.SL.$err.'.php';
                die();
            }

            KMlog::alarm('HTTP', 'Error page not found');
        }


        function host()
        {
            if(!isset(self::$host))
            {
                $s = strtolower( HOST );
                if(substr($s,0,4) == 'www.')
                    $s = substr($s,4);
            
                self::$host = $s;
            }

            return self::$host;
        }

        function _pp($v)
        {
            if(is_array($v))
            {
                $kk = array_keys($v);
                foreach($kk as $k)
                    $v[$k] = self::_pp($v[$k]);

                return $v;
            }
            else
                return stripslashes($v);
        }

        function prepost()
        {
            // Stub for stupid hosters!
            if(get_magic_quotes_gpc() AND (count($_POST) > 0))
                $_POST = self::_pp($_POST);
        }
    }
?>
