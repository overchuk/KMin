<?
    KM::ns('class');
	KMclass::uses('type');

/*
 Datatype Integer
	
	data:
		min - minimal value, if not defined - no minimal value
		max - maximum value, --//--

*/
class KM_tint extends KM_type
{

	function js_check($fid, $id, $v)
	{
		KM::ns('html');
		KMhtml::js('kmin.validator');
		return 'return kmin.validator.num("'.$this->js_id($id, $fid).'", "'.$this->data['min'].'", "'.$this->data['max'].'");';
	}
}

?>
