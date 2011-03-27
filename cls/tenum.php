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
	function input($id, $fid, &$row)
	{
		KM::ns('html');
		$c = $this->name();
		if($c)
			$c = ' class="km-'.$c.'" ';
		return KMhtml::combobox($id, $this->data['values'], $row[$id], ' id="'.$fid.'_'.$id.'" '.$c);
	}

	function php_value($name, &$row, $post=null)
	{
		if(isset($post))
			$v = $post[$name];
		else
			$v = $_POST[$name];

		if(!isset($this->data['values'][$v]))
			return false;

		$row[$name] = $v;
		return true;
	}

	function admin_form($prefix)
	{
		KM::ns('html');

		if(is_array($this->data['value']))
			$v = implode('|', $this->data['value']);
		else
			$v = '';

		return KMhtml::help($prefix.'__help','types/enum',600).'<br />'.
					MSG_ENUM_VALUES.':<br> <input type="text" size="60" name="'.$prefix.'_vals" value="'.$v.'" />';
								
	}

    function sql($name, &$cs)
    {
        $cs[$name] = 'TINYINT(1) NOT NULL';
        return true;
    }


}

?>
