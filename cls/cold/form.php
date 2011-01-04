<?

KM::ns('class');
KMclass::uses();

class KM_form extends KM_
{

function init($in = null)
{
    $def = array(
         'html' => array(
                      'table' => 'cellspacing="0" cellpadding="2" border="0" width="100%" class="km-table"',
                      'th' => '',
                      'td' => ''
                  ),
        );

    $this->_init($def, $in);
}


function show($fid = '', $row)
{
	$a = $this->data['form']['action'];
	if($a)
		$a = ' action="'.$a.'"';
	$m = $this->data['form']['method'];
	if($m)
		$m = ' method="'.$m.'"';


	// XXX Need files support

	echo '<form '.$a.$m.$e.' >'.LF;
	echo '<table '.$this->data['html']['table'].' >'.LF;
	foreach($this->data['items'] as $id => $cls)
	{
		$c = KMclass::obj($cls);
		echo $c->tr($name, $fid, $cls['name'], $cls['descr'], $row);
	}
	echo '</table>'.LF.'</form>'.LF;
}


}

?>
