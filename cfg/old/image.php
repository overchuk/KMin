<?

// Images inside '/img/page/' directory
KMimage::$types['page'] = array(
	
	'type'   => 'exclusive',   // One image, named by ID
	'class'	 => 'xy',          // Inside X*Y rectangle
	'width'  => '300',
	'height' => '100',
	'color'  => '',            // empty => transparent, other => color of padding
	'ext'    => 'png'
		
);



?>
