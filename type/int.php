<?

	class T_int extends T_
	{
		var $min=0;
		var $max=0;

		function T_int($the_min=0, $the_max=0)
		{
			if($the_max < $the_min)
				$the_max = $the_min;

			$this->min = $the_min;
			$this->max = $the_max;
		}

		function to_cmdline()
		{
			return 'min='.$this->min.'&max='.$this->max;
		}

		function from_cmdline($param)
		{
			$c = cmdline($param);
			$the_min = intval($c['min']);
			$the_max = intval($c['max']);
			return new T_int($the_min, $the_max);
		}

		function from_post($name, $v = null)
		{
			if(!$v)
				$v = $_POST;

			$the_min = intval($v[$name.'_min']);
			$the_max = intval($v[$name.'_min']);
			return new T_int($the_min, $the_max);
		}

		function val($s)
		{
			$n = intval($s);
			if($n < $this->min)
				$n = $this->min;
			elseif($n > $this->max)
				$n = $this->max;

			return $n;
		}

		function input_admin($name, $form, $add=array())
		{
			return 'MIN: <input type="text" size="32">;&nbsp;&nbsp;MAX: <input type="text" size="32">';
		}
	}
?>
