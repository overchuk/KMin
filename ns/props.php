<?

class KMprops
{

function ps2form($fid, $ps)
{
	KM::ns('class');
	KM::ns('html');

	foreach($ps as $n => $v)
	{
		$t = KMclass::obj($v['type']);
		$ps[$n]['type'] = $t;

		$scr .= 'function '.$fid.'_'.$n.'_check(){'.LF.$t->js_check($fid, $n, $v).LF.'}'.LF;

		$war = $v['warning'];
		if(!$war)
			$war = MSG_INVALID_VALUE.': '.$v['title'];
		
		$ons .= 'if( ! '.$fid.'_'.$n.'_check() ) {
	
						alert("'.KMhtml::val($war).'");	
						'.$t->js_focus($fid, $n, $v).'
						return false;
					}'.LF;
			
		$tab .= $t->tr($fid, $n, $v);
	}

	echo KMhtml::script($scr.'
		function '.$fid.'_on_submit(){
					'.$ons.'		
					return true;		
				}'.LF);

	echo KMhtml::tag('table', $tab);
}


function form2ps($post = null)
{
if(!isset($post))
	$post = $_POST;

}


}	

?>
