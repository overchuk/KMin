<?
    // XXX Need to customize for large page count

    KM::ns('url');
    KM::ns('html');

    KMhtml::css('pager');


    var_dump($data);

    echo '<div class="km-pager"><span class="km-pager-title">'.MSG_PAGES.':</span>'.LF;

    for($i=0; $i<$data['pages']; $i++)
    {
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

    echo '</div>'.LF;
?>
