<?
    KM::ns('log');
    KM::ns('url');
    KM::ns('html');
    KM::ns('http');
    KM::ns('msg');
    KM::ns('db');

    /*
     *
     * Use: function task_<taskname>($task, $table, $cfg) { ... }
     * for task named by <taskname> processing.
     *
     * If no more processing needed, returns display content,
     * If redirect needed, call redirect inside task function
     *
     */


    function task_common($task, $table, $cfg)
    {
        if(!isset($cfg['id']))
            $id = $task; 

        $func = 'task_'.$task;
        if(function_exists($funt) && ($task != 'common'))
            return $func($task, $table, $cfg);
    }

    function task_confirm_mdel($task, $table, $cfg)
    {
        $id = $cfg['id'];
        if(!isset($id))
            $id = $task;

        $count = count($_POST['ids']);
        if($count < 1)
            KMhttp::redirect();

        foreach($_POST['ids'] as $n => $v)
            $ret .= KMhtml::hidden("ids[$n]", $v);

        $set = KMdb::set(array_keys($_POST['ids']));
        $res = KMdb::sql_query('SELECT * FROM `#__'.$table.'` WHERE `id` IN '.$set);

        // True count
        $ns  = KMdb::num($res);
        if($ns < 1)
            KMhttp::redirect();

        while($r = KMdb::fetch($res))
            $inner .= KMhtml::val($r['id'].' '.$r['name']).'<br>'.LF;

        return KMhtml::divi('div_'.$form, 'km-div-form km-div-form-confirm',
                KMhtml::spoller('confirmsp', MSG_CMDEL_TITLE, $inner).
                '<br><br>'.
                KMhtml::form('form_'.$id).
                KMhtml::spani('span_'.$id, 'km-label km-label-confirm', KMmsg::val(MSG_CONFIRM_MDEL, array('count'=>$ns))).
                KMhtml::hidden('task', 'mdel').
                KMhtml::ok(MSG_YES).
                KMhtml::ok(MSG_NO, 'confirm_no').
                $ret.
                KMhtml::_('form'));

    }


    function task_mdel($task, $table, $cfg)
    {
        if(!isset($_POST['confirm_no']))
        {
            KMdb::del($table, array_keys($_POST['ids']));
            $cb = 'task_mdel_callback';
            if(function_exists($cb))
                $cb(array_keys($_POST['ids']));
        }
        

        KMhttp::redirect();
    }

    function task_del($task, $table, $cfg)
    {
        KMdb::del($table, $_POST['id']);
        $cb = 'task_del_callback';
        if(function_exists($cb))
            $cb($_POST['id']);

        KMhttp::redirect();
    }

    function task_update($task, $table, $cfg)
    {
        $r = '';
        $fs = $cfg['fields'];
        if(!is_array($fs))
            KMlog::alarm('TASK', 'No fields in task configuration: '.$task, array($table, $cfg));
        else
            foreach($fs as $f)
                $r[$f] = KMdb::val($_POST[$f]);

        $id = intval($_POST['id']);
        if($id == 0)
            KMdb::insert($table, $r);
        else
            KMdb::kupdate($table, $r, 'id', $id);

        if($cfg['continue'])
        {
            if($id == 0)
                $id = KMdb::insert_id();
            KMhttp::redirect( KMurl::set('id', $id) );
        }
        else 
            KMhttp::redirect( KMurl::clear('id') );
    }
?>
