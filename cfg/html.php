<?
	KM::ns('lang');

    // CSS files
    KMhtml::$can_css = array(
							'lightbox' => WEB_CSS.SL.'lightbox.css',
                            'spoller' => WEB_CSS.SL.'spoller.css',
                            'table' => WEB_CSS.SL.'table.css',
                            'pager' => WEB_CSS.SL.'pager.css',
							're' => WEB_CSS.SL.'re.css',

                            'admin' => WEB_ADMIN.'/css/style.css',
							'style' => WEB_SITE.'/css/style.css'
                        );

	// JS files
    KMhtml::$can_js = array(
                        'jquery' => WEB_JS.SL.'jquery.js',
                        'table' => WEB_JS.SL.'table.js',
                        'date' => WEB_JS.SL.'date.js',
                        'spoller' => WEB_JS.SL.'spoller.js',
                        'km18' => WEB_JS.SL.'km18.js',
                        'forms' => WEB_JS.SL.'forms.js',
						'lightbox' => WEB_JS.SL.'jq.lightbox.js',

                        'kmin' => WEB_JS.SL.'kmin.js',
                        'kmin.re' => WEB_JS.SL.'kmin.re.js',
                        'kmin.def' => WEB_JS.SL.'kmin.const.js',
                        'kmin.store' => WEB_JS.SL.'kmin.store.js',
                        'kmin.validator' => WEB_JS.SL.'kmin.validator.js',
                        'kmin.rowedit' => WEB_JS.SL.'kmin.rowedit.js',

                        'tiny' => WEB_SITE.'/TinyMCE/jscripts/tiny_mce/tiny_mce.js'
                );

    // JS Dependences
    KMhtml::$dep_js = array(
                    'spoller' => array('jquery'),
                    'date' => array('jquery'),
                    'table' => array('jquery'),
					'lightbox' => array('jquery', 'kmin.const'),
                    'kmin' => array('jquery'),
                    'kmin.re' => array('kmin'),
                    'kmin.def' => array('kmin'),
                    'kmin.validator' => array('kmin'),
                    'kmin.rowedit' => array('kmin.def')
                );

	// Initial js code for each script
	KMhtml::$init_js = array(
					'kmin.re'  => KMlang::js_init(),
					'kmin.def' => KM::js_const(),
				);

	// CSS binding to JS files
	KMhtml::$dep_js_css = array(
					'spoller'  => array('spoller'),
					'kmin.re'  => array('re'),
					'lightbox' => array('lightbox'),
				);

?>
