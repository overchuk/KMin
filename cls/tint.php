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

	function php_value($name, &$row, $post = null)
	{
		if(isset($post))
			$i = intval($post[$name]);
		else
			$i = intval($_POST[$name]);

		if(isset($this->data['min']))
		{
			if($i < intval( $this->data['min'] ))
				return false;
		}

		if(isset($this->data['max']))
		{
			if($i > intval( $this->data['max'] ))
				return false;
		}

		$row[$name] = $i;
		return true;
	}

	function admin_form($prefix, $inner = false)
	{
		KM::ns('html');

		return KMhtml::help($prefix.'__help','types/int',600, $inner).'<br />'.
				MSG_MIN.': <input type="text" size="6" name="'.$prefix.'_min" value="'.$this->data['min'].'" /><br />'.
				MSG_MAX.': <input type="text" size="6" name="'.$prefix.'_max" value="'.$this->data['max'].'" />';
								
	}

	function admin_data($prefix, $post=null)
	{
		return self::_admin_data($prefix, array('min' => 'intval', 'max' => 'intval'), $post);
	}

}

?>
