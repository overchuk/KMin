<?
	KM::ns('url');
	KM::ns('html');

    $c = $this->data['page_count'];
    if($c >1 )
    {


    KMhtml::css('pager');

    $u = $this->data['url'];
    $p = $this->data['page'];
    $dt = intval($this->data['dt']);

    $a  = $this->data['html']['a'];
    $div  = $this->data['html']['div'];
    $span  = $this->data['html']['span'];
    $spana  = $this->data['html']['span_active'];

    function tmpl_pager_1($i,$p, $u)
    {
        if($i == $p)
            echo '<span '.$spana.'>'.($i+1).'</span>'.LF;
        elseif($i == 0)
            echo '<span '.$span.'><a '.$a.' href="'.KMurl::clear($u, ($i+1)).'">'.($i+1).'</a></span>'.LF;
        else
            echo '<span '.$span.'><a '.$a.' href="'.KMurl::set($u, ($i+1)).'">'.($i+1).'</a></span>'.LF;
    }

    echo '<div '.$div.'>'.LF;

    if($dt && ($c > $dt))
    {
        if($p > 3)
        {
            tmpl_pager_1(0,$p,$u);
            echo '<span>...</span>';
            tmpl_pager_1($p-1,$p,$u);
            $min = $p;
        }
        else
            $min = 0;

        $max = min($p+2, $c);
        for($i=$min; $i < $max; $i++)
            tmpl_pager_1($i,$p,$u);

        if(($c - $p) > 4)
        {
            echo '<span>...</span>';
            $min = $c-1;
        }
        else
            $min = $p+2;

        $max = $c;
        for($i=$min; $i < $max; $i++)
            tmpl_pager_1($i,$p,$u);

    }
    else
    {
        for($i=0; $i<$c; $i++)
            tmpl_pager_1($i,$p,$u);
    }

    echo '</div>'.LF;

    }
?>
