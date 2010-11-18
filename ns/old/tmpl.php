<?
/*
	ns('const');
	ns('block');
	ns('place');
 */

	class KMtmpl
	{
		function root($name)
		{
			include DIR_TEMPLATE.SL.$name.'.php';			
		}


		function load($type, $name)
		{
			if($type)
				self::root( $type.SL.$name );
			else
				self::root( $name );
		}



	}


/*
	function _t($type='', $name)
	{
		KMtmpl::load($type, $name);
	}

	function _b($name)
	{
		KMblock::load($name);
	}

	function _p($name)
	{
		KMplace::load($name);
	}

	function _c($type='html', $name)
	{
		echo KMconst::val($type, $name);
	}
*/
?>
