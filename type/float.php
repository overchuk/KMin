<?

	class T_float extends T_
	{
		$min;
		$max;

		function T_float($the_min, $the_max)
		{
			if($the_max < $the_min)
				$the_max = $the_min;

			$this->$min = $the_min;
			$this->$max = $the_max;
		}

		function to_cmdline()
		{
			return 'min='.$this->$min.'&max='.$this->$max;
		}

		function from_cmdline($param)
		{
			$c = cmdline($param);
			$the_min = floatval($c['min']);
			$the_max = floatval($c['max']);
			return new T_float($the_min, $the_max);
		}

		function from_post($name, $v = null)
		{
			if(!$v)
				$v = $_POST;

			$the_min = floatval($v[$name.'_min']);
			$the_max = floatval($v[$name.'_min']);
			return new T_float($the_min, $the_max);
		}

		function val($s)
		{
			$n = floatval($s)
			if($n < $this->$min)
				$n = $this->$min;
			elseif($n > $this->$max)
				$n = $this->$max;

			return $n;
		}

		function input_admin($name, $form, $add=array())
		{
			echo 'MIN: <input type="text" size="32">;&nbsp;&nbsp;MAX: <input type="text" size="32">';
		}

?>
