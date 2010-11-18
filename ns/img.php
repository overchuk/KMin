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


    return array(
               'img' =>    $img,
               'x'   =>    $ip[0],
               'y'   =>    $ip[1]
           );
}

function rgb($color)
{
    return array(
            'r' => hexdec(substr($color,0,2)),
            'g' => hexdec(substr($color,2,2)),
            'b' => hexdec(substr($color,4,2)),
    );
}

function fill($img, $r, $g, $b)
{
    $img_color  = imagecolorallocate($img,  $r, $g, $b);
    imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $img_color);
}


function create($w,$h, $fill='')
{
    $ret = imagecreatetruecolor($w, $h);
    imagealphablending($ret, false);
    imagesavealpha($ret, true);

    if($fill)
    {
        $c = self::rgb($fill);
        self::fill($ret, $c['r'], $c['g'], $c['b']);
    }

    return $ret;
}


}



?>
