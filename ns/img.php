<?

class KMimg
{

function open($in)
{
    $ip = @getimagesize($in);
    switch($ip[2])
    {
        case 1:
            $img = imagecreatefromgif($in);
        break;
        case 2:
            $img = imagecreatefromjpeg($in);
        break;
        case 3:
            $img = imagecreatefrompng($in);
            imagesavealpha($img, true);
        break;
        default:
            return false;
    }


    return $img;
}

function w($img)
{
	return imagesx($img);
}

function h($img)
{
	return imagesy($img);
}

function rgb($color)
{
    return array(
            'r' => hexdec(substr($color,0,2)),
            'g' => hexdec(substr($color,2,2)),
            'b' => hexdec(substr($color,4,2)),
    );
}

function fill($img, $r, $g=null, $b=null)
{
	if(!isset($g))
	{
		$r = self::rgb($r);
		$g = $r['g'];
		$b = $r['b'];
		$r = $r['r'];
	}
    $img_color  = imagecolorallocate($img,  $r, $g, $b);
    imagefilledrectangle($img['img'], 0, 0, imagesx($img), imagesy($img), $img_color);
}


function create($w,$h, $fill=null)
{
    $ret = imagecreatetruecolor($w, $h);
    imagealphablending($ret, false);
    imagesavealpha($ret, true);

    if($fill)
        self::fill($ret, $fill);

    return $ret;
}

/*
	img = incoming image

	width,height = Rectangle of result image
			if one of these is zero, user proportional size,
		    use padding otherwise.

	Options:
		x,y,w,h: Use it area of image, instead of full image
		fill   : hex color for padding, default => transparent
		delta  : How many pixels of padding, fill by scretch, default no scratch
		nopad  : If true, use width,height only as maximun, result image has no padding.
                 Default false => result image will be width*height
		bigger : Increase incoming image if neccessary
*/
function resize($img, $width, $height, $ops = array())
{
	// Only one dimention can be zero
	if(($width < 1) && ($height < 1))
		return false;

	// Create true incoming area
	// No zero demention should be.
	$dx = intval($ops['x']);
	$dy = intval($ops['y']);
	$dw = intval($ops['w']);
	if($dw == 0)
		$dw = self::w($img) - $dx;
	if($dw < 1)
		return false;
	$dh = intval($ops['h']);
	if($dh == 0)
		$dh = self::h($img) - $dy;
	if($dh < 1)
		return false;

	// Flag b => image can be bigger
	$b = $ops['bigger'] ? true : false;

	// Delta pixels to use scretch instead of pad
	$d = intval($ops['delta']);

	// Scale coefs for X and Y	
	$kx = $width / $dw;
	if(($kx > 1) && (!$b))
		$kx = 1;
	$ky = $height / $dh;
	if(($ky > 1) && (!$b))
		$ky = 1;


	$same = false;
	
	// If nopadding, use only min coef
	if($ops['nopad'])
	{
		if($kx < $ky)
		{
			$ky = 0;
			if($kx == 1)
				$same = true;
		}
		else
		{
			$kx = 0;
			if($ky == 1)
				$same = true;
		}
	}
	

	if($same)
	{
		$width  = $dw;
		$height = $dh;
		$rx = 0;
		$ry = 0;
		$rw = $width;
		$rh = $height;
	}	
	elseif($kx == 0)
	{
		$width = intval($dw*$ky);
		$rx = 0;
		$ry = 0;
		$rw = $width;
		$rh = $height;
	}
	elseif($ky == 0)
	{
		$height = intval($dh*$kx);
		$rx = 0;
		$ry = 0;
		$rw = $width;
		$rh = $height;
	}
	elseif($kx < $ky)
	{
		$rx = 0;
		$rw = $width;

		$rh = intval($height*$kx);
		if(($height - $rh) > $d)
			$ry = intval( ($height - $rh)/2 );
		else
		{
			$ry = 0;
			$rh = $height;
		}

	}
	else
	{
		$ry = 0;
		$rh = $height;

		$rw = intval($width*$ky);
		if(($width - $rw) > $d)
			$rx = intval( ($width - $rw)/2 );
		else
		{
			$rx = 0;
			$rw = $width;
		}
	}

	$ret = self::create($width,$height, $ops['fill']);
    if(!imagecopyresampled($ret, $img, $rx,$ry,$dx,$dy,$rw,$rh,$dw,$dh))
    {
        imagedestroy($ret);
        return false;
    }

	return $ret;
}





// Save as..

function jpg($img, $path, $q=100)
{
	return @imagejpeg($img, $path, $q);
}

function png($img, $path)
{
	return @imagepng($img, $path);
}


}



?>
