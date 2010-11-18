<?

class KMimage
{

/*
	dirname (from '/img/') => array of option:
 */
	static public $types = array();


	static private $_ts = array(

			// One image named by ID of object
		'exclusive' => 1,

			// Shared images, any objects can show these images
		'shared' => 2,


	);

	static $_cs = array(
		'xy'   => 1,      // In XY rectangle
		'max'  => 2,      // Max demension is max
		'maxx' => 3,      // x-demension is width
		'maxy' => 4,      // y-demention is height
		'asis' => 5,      // Image as is
	);

	function upload($type, $file)
	{
			
	}

}


?>
