<?
    KM::ns('log');
    KM::ns('http');

    class KMaccess
    {
        function check($task, $table)
        {
            // XXX
            // Need to implement: Use predefined access rights, check current user

            return true;
        }

        function right($task, $table)
        {
            if(!self::check($task,$table))
            {
                KMlog::alarm('ACCESS', 'Access forbidden');
                KMhttp::error('403');
            }
        }    
    }

?>
