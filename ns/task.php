<?

    KM::ns('util');
    KM::ns('access');

    class KMtask
    {
        static $common = array();

        /*
            Process pre-defined tasks from _POST (or $var)
            returns content if whole request is already processed,
            (for example, if CONFIRM form is displayed)
        */
        function process($name, $in)
        {
            // var_dump($_POST);


            $cfg = array(
                    'dir' => DIR_TASK,
                    'tag' => 'task',
                    'task' => self::$common
                );

            KMutil::update($cfg, $in); 

            if($var == null)
                $var = $_POST;
    
            $tag   = $cfg['tag'];
            $table = $cfg['table'];
            $task  = $_POST[$tag];



            if(isset($cfg['task'][ $task ]))
            {
                KMaccess::right($task, $table);

                if($cfg['dir'])
                    $name = $cfg['dir'].SL.$name.'.php';

                // Include task definitions
                include_once($name);
                
                $func = $cfg['func'];
                if(!isset($func))
                    $func = 'task_'.$task;

                // die("<h1>-- $func --</h1>");

                if(function_exists($func))
                    $ret = $func($task, $table, $cfg['task'][$task]);
            }

            
            if($ret)
                return $ret;

            if(isset($cfg['redirect']))
                KMhttp::redirect();

            return '';
        }
    }


?>
