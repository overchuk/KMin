<?
    
KM::ns('db');
KM::ns('util');
KM::ns('class');
KMclass::uses();

class KM_pager extends KM_
{
    
function init($in)
{
    $def = array(
                    'on_page' => 20, 
                    'dt' => 10,
					'url' => 'p',
                );

    KMutil::update($def, $in);
    parent::init($def);

    $this->data['total'] = KMdb::col( KMdb::sql_query( $this->data['sql']['count'] ) );
    $this->data['page_count'] = intval( ($this->data['total'] -1) / $this->data['on_page'] ) +1;
    $page = intval( $_GET[ $this->data['url'] ] -1);
    if($page < 0)
        $page = 0;
    if($page >= $this->data['page_count'])
        $page = 0;

    $this->data['page'] = $page;
}

function query($order)
{
    return KMdb::sql_query( $this->data['sql']['select'] . 
                                ' ORDER BY '.$order.
                                ' LIMIT '.intval($this->data['page'] * $this->data['on_page']).', '.$this->data['on_page'] );
} 

}

?>
