<?

	class T_string extends T_
	{
		var $mask = '';

		function T_string($the_mask='')
		{
			$mask = $the_mask;
		}

		function to_cmdline()
		{
			return 'mask='.$mask;
		}

		function from_cmdline($param)
		{
			$c = cmdline($param);
			return new T_string($c['mask']);
		}

		function from_post($name, $v = null)
		{
			if(!$v)
				$v = $_POST;

			return new T_string($v[$name]);
		}

		function val($s)
		{
			if(preg_match($this->$mask, $s))
				return $s;
			else
			{
				$this->error(TX_TP_STRING_ERROR_MASK);
				return false;
			}
		}

		function input_admin($name, $form, $add=array())
		{
			echo 'Regexp: <input type="text" name="'.$name.'" id="'.$name.'" value="'.$this->$mask.'">
						<br><font size=-2>Если вы не знаете что это, оставьте поле пустым</font>';
		}
	}
?>
