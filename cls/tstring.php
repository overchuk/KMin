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

	function php_value($name, &$row, $post=null)
	{
		if(isset($post))
			$s = $post[$name];
		else
			$s = $_POST[$name];

		$l = mb_strlen($s);

		if(isset($this->data['min']))
			if($l < intval($this->data['min']))
				return false;
		if(isset($this->data['max']))
			if($l > intval($this->data['max']))
				return false;

		if(!isset($this->data['mask']))
		{
			$row[$name] = $s;
			return true;
		}

		if(!preg_match('/'.$this->data['mask'].'/'.$this->data['mask_i'], $s))
			return false;

		$row[$name] = $s;
		return true;
	}
}

?>
