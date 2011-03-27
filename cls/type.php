<?
    KM::ns('class');

/*
Root class for KMin data types
*/
class KM_type extends KM_
{

	/*
		============= HTML side
	*/



	/* 
		Show HTML input element(s) by datatype
		$id    - property name
		$fid   - id of HTML form
		$value - Current value of property

		by default, just display value.
	*/
	function input($id, $fid, &$row)
	{
		KM::ns('html');
		$c = $this->name();
		if($c)
			$c = 'class="km-'.$c.'"';
		return '<input type="text" id="'.$this->js_id($id, $fid).'" name="'.$id.'" '.
					$c.' value="'.KMhtml::val($row[$id]).'" />'.LF;
	}

	function js_id($id, $fid)
	{
		return $fid.'_'.$id;
	} 


	function td($fid, $n, &$v, &$row)
	{
		return '<td>'.$this->input($n, $fid, $row).'</td>';
	}

	function th($fid, $n, &$v, &$row)
	{
		return '<th>'.$v['title'].'<br><font size=-2>'.$v['descr'].'</font></th>';
	}

	function tr($fid, $n, &$v, &$row)
	{
		return '<tr>'.$this->th($fid, $n, $v, $row).$this->td($fid, $n, $v, $row).'</tr>'.LF;
	}

	function js_check($fid, $n, &$v)
	{
		return 'return true;';
	}

	function js_focus($fid, $n, &$v)
	{
		return 'document.getElementById("'.$this->js_id($n, $fid).'").focus();';
	}

	function html($name, &$row)
	{
		return $row[ $name ];
	}

	/*
		============ PHP side
	*/


	/*	Check and set value on PHP side	*/
	function php_value($name, &$row, $post=null)
	{
		if(!isset($post))
			$post = $_POST;

		$row[$name] = $post[$name];
		return true;
	}	




	/*	
		=========== ADMIN side
	*/

	function admin_form($prefix)
	{
		//return '<strong>'.$prefix.' - '.$this->name().'</strong>';
		return '<strong>'.$prefix.' - '.$this->name().'</strong>';
	}


	function admin_data($prefix, $post=null)
	{
		return array();
	}

	function _admin_data($prefix, $items, $post = null)
	{
		if(!isset($post))
			$post = &$_POST;

		$data = array();
		foreach($items as $i => $t)
		{
			$v = $post[ $prefix.'_'.$i ];
			if($v !== '')
				$data[$i] = function_exists($t) ? $t($v) : $i;
		}
		return $data;
	}

	function admin_load($prefix, $post = null)
	{
		KM::ns('util');
		KMutil::update($this->data, $this->admin_data($prefix, $post));
	}




	/*
		============ SQL side
	*/

    /*
        Get array(sql_column_id => sql_column_type)
        needed to store values
    */
    function sql($name, &$cs)
    {
        return true;
    }

}

?>
