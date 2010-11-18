<?

    // Host-dep mysql configuration
    $cfgs = array(
        'pro-mama.ru' => array("u54666.mysql.masterhost.ru", "u54666", "sti5nitickb", "u54666_2", "pro_"),
        '' => array('localhost', 'root', 'riman', 'pm', 'pro_')
    );






    // ===========================================

    KM::ns('http');

    $s = KMhttp::host();
    if(!isset($cfgs[$s]))
        $s = '';

	KMdb::$host   = $cfgs[$s][0];
	KMdb::$user   = $cfgs[$s][1];
	KMdb::$pass   = $cfgs[$s][2];
	KMdb::$base   = $cfgs[$s][3];
	KMdb::$prefix = $cfgs[$s][4];
?>
