<?

class KMprops
{

function ps2form($fid, &$ps, $row)
{
	KM::ns('class');
	KM::ns('html');

	if(!is_array($row))
		$row = array();

	foreach($ps as $n => $v)
	{
		$t = KMclass::obj($v['type']);
		//$ps[$n]['type'] = $t;

		$scr .= 'function '.$fid.'_'.$n.'_check(){'.LF.$t->js_check($fid, $n, $v).LF.'}'.LF;

		$war = $v['warning'];
		if(!$war)
			$war = MSG_INVALID_VALUE.': '.$v['title'];
		
		$ons .= 'if( ! '.$fid.'_'.$n.'_check() ) {
	
						alert("'.KMhtml::val($war).'");	
						'.$t->js_focus($fid, $n, $v).'
						return false;
					}'.LF;
			
		$tab .= $t->tr($fid, $n, $v, $row);
	}

	echo KMhtml::script($scr.'
		function '.$fid.'_on_submit(){
					'.$ons.'		
					return true;		
				}'.LF);

	echo KMhtml::tag('table', $tab);
}


function form2value($ps, $post = null)
{
	KM::ns('class');

	$row = array();
	foreach($ps as $n => $v)
	{
		$t = KMclass::obj($v['type']);
		if(!$t->php_value($n, $row, $post))
			return $n;
	}

	return $row;
}


/*

 Draw form for editing properties set $ps
 or processing this form.
 If $_POST present, processed data, change $ps 
 and return true

*/
function edit($fid, &$ps)
{
	if($_POST['task'] == $fid.'_props_edit')
	{
		// XXX
		$ps = array('one', 'two');
		return true;
	}
	
	KM::ns('html');
	KMhtml::js('kmin.rowedit');


	echo '<form method="POST" id="'.$fid.'"><input type="hidden" name="task" value="'.$fid.'_props_edit">
	<table id="'.$fid.'__table" width="100%" cellspacing="0" cellpadding="3" border="1">'.LF;


	echo '</table>'.LF;	

	echo '<input type="text" id="'.$fid.'__name" value="aa" />';	
	echo '<span style="cursor:pointer;" 
	onclick="'.$fid.'__sz = kmin.rowedit.add(\''.$fid.'\', document.getElementById(\''.$fid.'__name\').value);">INSERT</span>';


}


}	

?>
