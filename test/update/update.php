<?
    include '../config.php';
    kmin_import('ns');
    
    KM::ns('util');


    $one = array('a' => '11',
                    'b' => array( 'a' => '12', 'c'=>array('rr'=>1, 'dd'=>2), 'd'=>44),
                    'c' => array()
                );

    $two = array(
            'd' => '83',
            'e' => array('aa'=>'ab', 'bb'=>array('a'=>1, 'b'=>2)),
            'b' => array('d'=>15, 'c'=>array('v'=>array('zz'=>4, 'yy'=>5), 'dd'=>3))
        );


    var_dump($one);
    KMutil::update($one, $two);
    echo '<hr>';
    var_dump($one);
?>
