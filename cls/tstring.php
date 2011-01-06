<?
    KM::ns('class');
	KMclass::uses('type');

/*
 Datatype String
	
	data:
		mask - RegExp, value should be matched if mask present

*/
class KM_tstring extends KM_type
{
	function js_check($fid, $id, $v)
	{
		KM::ns('html');
		KMhtml::js('kmin.validator');
		return 'return kmin.validator.str("'.$this->js_id($id, $fid).'", "'.
						$this->data['min'].'", "'.
						$this->data['max'].'", "'.
						str_replace("\\", "\\\\", $this->data['mask']).'");';
	}
}

?>
