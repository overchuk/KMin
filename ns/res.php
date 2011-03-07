<?
class KMres
{

static $_types = array();
static $_res = array();

function show($name, $tmpl=null)
{
    $r = &self::$_res[$name];
    if(!is_array($r))
        return false;

    $t = KMclass::obj($r, true, true);
    $t->show($tmpl);

    return true;
}

}
?>
