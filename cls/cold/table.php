<?

KM::ns('log');
KM::ns('url');
KM::ns('html');
KM::ns('class');

KMclass::uses();

class KM_table extends KM_
{

function init($in = null)
{
    $def = array(
        'pager' => array(
                    'class' => 'pager',
                    'data' => array(
                            'url' => 'p'
                        )),

        'sort' => array(
                        'field' => 's',       
                        'dir' => 'd',          
                        'default' => '`id` ASC',
                        'asc' => TEXT_ORDER_ASC,
                        'desc' => TEXT_ORDER_DESC
                    ),

         'html' => array(
                      'table' => 'cellspacing="0" cellpadding="2" border="1" width="100%" class="km-table"',
                      'th' => '',
                      'td' => ''
                  ),

         'elements' => array(
                              'pager_up' => false,
                              'pager_down' => true
                            )
        );

    $this->_init($def, $in);
}

function orderby()
{
    $s = $_GET[ $this->data['sort']['field'] ];
    if( !isset($s) )
        return $this->data['sort']['default'];

    $s = $this->data['cells'][ $s ]['sort'];
    if(!$s)
        KMlog::error(403);

    $d = $_GET[ $this->data['sort']['dir'] ] ? 'DESC' : 'ASC';

    return $s.' '.$d;
}

function th($id, $txt, $purl)
{
    if($id == $_GET[ $this->data['sort']['field'] ])
    {
        $d = ($_GET[ $this->data['sort']['dir'] ]) ? 1 : 0;
        $cx = $d;
    }
    else
    {
        $d = 1;
        $cx = 2;
    }

    if($d == 0)
        $u = KMurl::set(array(
            $this->data['sort']['field'] => $id,
            $this->data['sort']['dir'] => 1,
            $purl => 0
            ));
    else
        $u = KMurl::cs(array($this->data['sort']['dir']), array($this->data['sort']['field'] => $id, $purl => 0));
 
    $t = $d ? $this->data['sort']['asc'] : $this->data['sort']['desc'];

    return '<a href="'.$u.'" title="'.$t.'">'.$txt.'<span class="km-table-order-'.$cx.'">&nbsp;&nbsp;&nbsp;</span></a>';
}

function show($fid = '')
{
    KMhtml::css('table');

    $pager = KMclass::obj( $this->data['pager'] );
    $res = $pager->query( $this->orderby() );

    $pager_html = $pager->_html();

    $cs = array();
    foreach( $this->data['cells'] as $id => $c)
        $cs[ $id ] = KMclass::obj($c);

    ob_start();

    if($this->data['elements']['pager_up'])
        echo $pager_html;

    echo '<table '.$this->data['html']['table'].'>'.LF;
    echo '<tr '.$this->data['html']['trh'].'>';
    foreach($cs as $id => $c)
    {
        $txt = $c->th($id, $fid);
        if( $this->data['cells'][$id]['sort'] )
            $txt = $this->th($id, $txt, $pager->data['url']);

        $th = $c->data['html']['th'];
        if(!$th)
            $th = $this->data['html']['th'];

        echo '<th '.$th.'>'.$txt.'</th>';
    }

    echo '</tr>'.LF;

    $i = 0;
    while($row = KMdb::fetch($res))
    {
        echo '<tr '.$this->data['html']['tr'.$i].'>';
        $i = 1-$i;
        foreach($cs as $id => $c)
        {
            $td = $c->data['html']['td'];
            if(!$td)
                $td = $this->data['html']['td'];

            echo '<td '.$td.'>'.$c->td($id, $fid, $row).'</th>';
        }
        echo '</tr>'.LF;
    }

    echo '</table>'.LF;

    if($this->data['elements']['pager_down'])
        echo $pager_html;

    $ret = ob_get_contents();
    ob_end_clean();

    return $ret;
}


}

?>
