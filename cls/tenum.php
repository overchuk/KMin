<?
    KM::ns('class');
	KMclass::uses('type');

/*
 Datatype Enumiration
	
	data:
		values - Array (value => title)

*/
class KM_tenum extends KM_type
{
	function input($id, $fid, $value)
	{
		KM::ns('html');
		$c = $this->name();
		if($c)
			$c = ' class="km-'.$c.'" ';
		return KMhtml::combobox($id, $this->data['values'], $value, ' id="'.$fid.'_'.$id.'" '.$c);
	}
}

?>
