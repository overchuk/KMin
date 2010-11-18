<?
    $ops = '';
    $w = $data['*']['width'];
    if(isset($w))
        $ops .= " width=\"$w\"";
    $h = $data['*']['height'];
    if(isset($h))
        $ops .= " height=\"$h\"";

    foreach($data as $n => $v)
    {
        if(!isset($v['icon']))
            continue;

        $a = '<a title="'.$v['title'].'"';
        if(isset($v['href']))
            $a .= ' href="'.$v['href'].'"';        
        if(isset($v['onclick']))
            $a .= ' onclick="'.$v['onclick'].'"';        
        
        echo $a.'>'.KMhtml::img(WEB_ICON.SL.$v['icon'], $v['title'], $ops).'</a>&nbsp;'.LF;
    }
?>
