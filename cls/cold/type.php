<?

KM::ns('class');
KMclass::uses();

class KM_type extends KM_
{

	function init()
	{}


	// DRAW 
	function input($fid, $id, $row)
	{
		return '';
	}

	function th($name, $descr)
	{
		return '<th>'.$name.'<br><font size="-2">'.$descr.'</font></th>';
	}

	function td($id, $fid, $row)
	{
		return '<td>'.$this->input($id,$fid,$row).'</td>';
	}

	function tr($id, $fid, $name, $descr, $row)
	{
		return '<tr>'.$this->th($name, $descr).$this->td($id, $fid, $row).'</tr>'.LF;
	}



	// JS
	function js_pre($id, $fid)
	{
		return '';
	}

	function js_check($id, $fid)
	{

	}

	
	// DB
	function sql($id)
	{
		return '';
	}
	

	// PHP
	function read($id, $fid, &$row)
	{
		return false;
	}

}

?>
