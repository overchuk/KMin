<?
error_reporting (E_ALL & ~E_NOTICE);
    include '../../../config.php';
    kmin_import('ns');
    
    KM::ns('util');

    $input = array(
        '',
        'b',
        '_b',
        'bb', 
        'abcZZkd',
        '1pp',
        'Port_19_ua',
        'port_19_ua',
        'aAa',
        'A',
        'дfff',
        'ffдff',
        'b_'
    );

    foreach($input as $i)
        echo '"'.$i.'" = '.(KMutil::isid($i) ? 'YES' : 'NO').LF;

?>
