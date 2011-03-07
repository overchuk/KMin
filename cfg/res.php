<?
    KMres::$_res = array(
        'main.text' => array(
                            'class' => 'r_text',
                            'data' => array(
                                        'type' => 0;
                                ),
                        ),

        'photogal' => array(
                            'class' => 'r_photos',
                            'data' => array(
                                        'type' => 0,
                                        'folder' => 'img/gal',
                                        'resize' => array(
                                                        'width' => 200,
                                                        ),
                                            ),
                            ),

    );

    KM::site_cfg('res');
?>
