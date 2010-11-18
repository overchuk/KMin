<?

    KM::ns('url');
    KM::ns('html');
    KMhtml::css('toolbar');


    echo '<div class="km-toolbar">'.LF;

    foreach($data as $n => $v)
    {
        switch($v['type'])
        {
            case 'url':
            break;

            case 'post':
            break;
        }

        if($i == $data['curr'])
        {
            echo '<span class="km-pager-curr">&nbsp;'.($i+1).'&nbsp</span>';
        }
        else
        {
            if($i == 0)
                $u = KMurl::clear($data['tag']);
            else
                $u = KMurl::set($data['tag'], $i);
            echo '<span class="km-page-num">&nbsp;<a href="'.$u.'">'.($i+1).'</a>&nbsp;</span>';
        }
    }

    echo LF.'</div>'.LF;
?>
