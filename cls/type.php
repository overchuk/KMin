<?
    KM::ns('class');

/*
Root class for KMin data types
*/
class KM_type extends KM_
{
	/* 
		Show HTML input element(s) by datatype
		$id    - property name
		$fid   - id of HTML form
		$value - Current value of property

		by default, just display value.
	*/
	function input($id, $fid, $value)
	{
		KM::ns('html');
		$c = $this->name();
		if($c)
			$c = 'class="km-'.$c.'"';
		return '<input type="text" id="'.$this->js_id($id, $fid).'" name="'.$id.'" '.$c.' value="'.KMhtml::val($value).'" />'.LF;
	}

	function js_id($id, $fid)
	{
		return $fid.'_'.$id;
	} 


	function td($fid, $n, &$v)
	{
		return '<td>'.$this->input($n, $fid, $v['value']).'</td>';
	}

	function th($fid, $n, &$v)
	{
		return '<th>'.$v['title'].'<br><font size=-2>'.$v['descr'].'</font></th>';
	}

	function tr($fid, $n, &$v)
	{
		return '<tr>'.$this->th($fid, $n, $v).$this->td($fid, $n, $v).'</tr>'.LF;
	}

	function js_check($fid, $n, &$v)
	{
		return 'return true;';
	}

	function js_focus($fid, $n, &$v)
	{
		return 'document.getElementById("'.$this->js_id($n, $fid).'").focus();';
	}

}

?>
