<?
	class T_
	{
		var $_error = '';
		
		function input($name, $form, $add=array())
		{
			return '<input type="text" size="32" name="'.$name.'" id="'.$form.'_'.$name.'">';
		}

		function sql($name)
		{
			 return array('`'.$name.'` INT( 11 )');
		}

		function sql_index($name)
		{
			return array('`'.$name.'`');
		}

		function error($err=null)
		{
			if(isset($err))
				$this->_error = $err;

			return $this->_error;
		}
	}
?>
