<?

KM::ns('class');
KMclass::uses('type');

class KM_type_enum extends KM_type
{

	function init($data)
	{
		$def = array(
				'minlength' => 0,
				'maxlength' => 255,
				'mask' => ''
			);

		$this->_init($def, $data);
	}


	// DRAW 
	function input($id, $fid, &$row)
	{
		if($this->data['maxlength'])
			$max = ' maxlength="'.$this->data['maxlength'].'"';

		return '<input type="input" name="'.$id.'" id="'.$fid.'_'.$id.'" value="'.KMhtml::val($row[$id]).'" '.$max.' '.$this->data['html']['input'].' />'.LF;
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
