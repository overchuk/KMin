<?

    KM::ns('log');
    KM::ns('lang');
    KM::ns('util');

	class KMhtml
	{
		// JS processing
        static $can_js      = array();
        static $dep_js      = array();
        static $init_js     = array();
        static $use_js      = array();
		static $already_js  = array();
		static $already_fjs = array();
		static $dep_js_css  = array();

		static $init_code = '';
      
		// CSS processing 
        static $can_css = array();
        static $use_css = array();

        static $use_re   = false; 
        static $use_tiny = false; 

		// Additional headers
		static $_head = array();

        function js($name)
        {
            $f = self::$can_js[$name];
            if(!isset($f))
            {
                KMlog::alarm('HTML', 'Unknown javascript: '.$name);
                return;
            }

            // File already included
            if(!self::$use_js[$name])
			{
				self::$use_js[$name] = true;
				if(is_array(self::$dep_js_css[$name]))
				foreach(self::$dep_js_css[$name] as $css)
					self::css($css);
            }
        }

        function css($name)
        {
            $f = self::$can_css[$name];
            if(!isset($f))
            {
                KMlog::alarm('HTML', 'Unknown stylesheet: '.$name);
                return;
            }

            // File already included
            if(isset(self::$use_css[$f]))
                return;
            
            self::$use_css[$f] = true;
        }

		function onejs($name)
		{
			if(self::$already_js[ $name ])
				return;

			self::$already_js[ $name ] = true;
		
			if(is_array(self::$dep_js[ $name ]))	
			foreach(self::$dep_js[ $name ] as $s)
				self::onejs( $s );

			self::$already_fjs[ self::$can_js[ $name ] ] = true;
			self::$init_code .= self::$init_js[$name];
		}

        function alljs()
        {
			self::$already_js = array();
			self::$already_fjs = array();
			self::$init_code = '';

			foreach(self::$use_js as $n => $v)
				self::onejs($n);
	
			$ret = '';
			foreach(self::$already_fjs as $f => $v)
				$ret .= '<script language="javascript" src="'.$f.'"></script>'.LF;

			if(self::$init_code)
				$ret .= '<script language="javascript">'.LF.self::$init_code.LF.'</script>'.LF;			

            return $ret;
        }

        function allcss()
        {
            foreach(self::$use_css as $f => $b)
                if($f)
                    $ret .= '<link href="'.$f.'" rel="stylesheet" type="text/css" />'.LF;

            return $ret;
        }

		function add_head($s)
		{
			self::$_head[] = $s;
		}

        function head($name = null)
        {
            if($name)
            switch($name)
            {
                case 'js':
                    return self::alljs();

                case 'css':
                    return self::allcss();

				case 'add':
					return join(LF, self::$_head);

                default:
                    KMlog::alarm('HTML', 'Invalid head block: '.$name);
            }
            else
            {
                $heads = array('js', 'css', 'add');
                $ret = '';
                foreach($heads as $h)
                    $ret .= self::head($h);
                return $ret;
            }
        }
        // =============================================================================


		function js_redirect($url)
		{
			echo '<script>
					 top.self.window.location = "'.$url.'";
					</script>'.LF;

		}

		function frame_go($url, $frame = null, $part='')
		{
			if($frame)
				$win = 'top.self.window.frames["'.$frame.'"]';
			else
				$win = 'top.self.window';

			if($part)
				$part = ".$part";

			echo '<script>
					'.$win.'.location'.$part.' = "'.$url.'";
				</script>'.LF;
		}

		function frame_hash($hash, $frame)
		{
			echo '<script>
					var d = top.self.window.frames["'.$frame.'"].contentDocument;
					var n = d.location.href.indexOf("#");
					var s = n==-1?d.locaction.href:d.location.href.slice(0,n);
					d.location.href = s+"#'.$hash.'";
				</script>'.LF;
		}

		function frame_reload($hash, $frame)
		{
			echo '<script type="text/javascript">
					var f = top.self.window.frames["'.$frame.'"];
					var d = f.contentDocument;
					if(!d) d = f.document;
					if(!d) d = f.contentWindow.document;
					alert(d);
					var n = d.location.href.indexOf("#");
					var s = n==-1?d.locaction.href:d.location.href.slice(0,n);
					s += "#'.$hash.'";
					alert(s);
					d.location = s;
					d.location.reload(s);
				</script>'.LF;
		}
		


        // Transform ===================================================================
		function val($s)
		{
			return htmlspecialchars($s);
		}

        function txt($s)
        {
            return strip_tags($s);
        }
        // =============================================================================


        // HTML elements ===============================================================
		function img($src, $alt, $ops='', $title='')
		{
			if($ops)
				$ops = " $ops";

			if(!isset($title))
				$title = $alt;

            if(isset($alt))
                $alt = ' alt="'.self::val($alt).'"';

            if(isset($title))
                $title = ' title="'.self::val($title).'"';

			return '<img src="'.$src.'"'.$alt.$title.$ops.' />';
		}

		function script($inner, $opt='', $lang='javascript')
		{
			if(!$inner)
				return '';

			if($opt)
				$opt = " $opt";

			return '<script language="'.$lang.'"'.$opt.'>'.LF.$inner.LF.'</script>'.LF;
		}

        function table($pad, $space, $border, $width=null, $opt=null)
        {
            if($opt)
                $opt = " $opt";

            if($width)
                $width=' width="'.$width.'"';

            echo '<table cellpadding="'.$pad.'" cellspacing="'.$space.'" border="'.$border.'"'.$width.$opt.'>'.LF;
        }

		function tag($name, $inner, $attr='', $lf=true)
		{
			return '<'.$name.' '.$attr.'>'.$inner.'</'.$name.'>'. ($lf ? LF : '');
		}

		function tr($inner, $attr='')
		{
			return self::tag('tr', $inner, $attr);
		}

		function td($inner, $attr='')
		{
			return self::tag('td', $inner, $attr, false);
		}

		function th($inner, $attr='')
		{
			return self::tag('th', $inner, $attr, false);
		}

		function div($inner, $attr='')
		{
			return self::tag('div', $inner, $attr);
		}
        // =============================================================================


        // i-functions,   elements with 'id' and 'class' attributes ==========
        function tagi($tag, $id, $class, $inner=null, $attr='')
        {
            if($attr)
                $attr = " $attr";
            if($id)
                $attr .= ' id="'.$id.'"';
            if($class)
                $attr .= ' class="'.$class.'"';

            $ret =  "<$tag $attr>";
            if(isset($inner))
                $ret .= "$inner</$tag>".LF;
            
            return $ret;
        }

        function spani($id, $class, $inner=null, $attr='')
        {
            return self::tagi('span', $id, $class, $inner, $attr);
        }

        function divi($id, $class, $inner=null, $attr='')
        {
            return self::tagi('div', $id, $class, $inner, $attr);
        }
        // ===================================================================
        

		function input($name, $value, $max='', $ops='')
		{
			if($max)
				$max = ' size="'.$max.'"';

			if($ops)
				$ops = " $ops";

			return '<input type="text" name="'.$name.'" value="'.self::val($value).'"'.$ops.$max.' />'.LF;
		}

        function hidden($name, $value, $ops='')
        {
			if($ops)
				$ops = " $ops";
           
            return '<input type="hidden" name="'.$name.'" value="'.self::val($value).'"'.$ops.' />'.LF;
        }

		function textarea($name, $value, $rows="10", $ops='')
		{
			if($ops)
				$ops = " $ops";

			return '<textarea name="'.$name.'" rows="'.$rows.'"'.$ops.'>'.self::val($value).'</textarea>'.LF;
		}


		function checkbox($name, $value, $ops='')
		{
			if($ops)
				$ops = " $ops";
			$chk = ($value ? ' checked="checked"' : '');
            if($name)
                $name = ' name="'.$name.'"';
			return '<input type="checkbox"'.$name.$ops.$chk.' />'.LF;
		}

		function combobox($name, $hash, $value, $ops='')
		{
			if($ops)
				$ops = " $ops";

			if(!isset($value))
			{
				$keys = array_keys($hash);
				$value = $keys[0];
			}

			foreach($hash as $n => $v)
			{
				$sel = ($n == $value) ? ' selected="selected"' : '';
				$ret .= self::tag('option', $v, $sel.' value="'.$n.'"');
			}	

			return self::tag('select', $ret, ' name="'.self::val($name).'" value="'.self::val($value).'"'.$ops);
		}

		function tiny($name, $form, $opt = array())
		{
       
        self::js('tiny'); 

        $def = array(
                    'css' => WEB_SITE.'/css/style.css',
					'b1' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bold,italic,underline,strikethrough,'.
							'|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,'.
							'|,undo,redo,|,formatselect,fontselect,fontsizeselect,help',

					'b2' => 'code,link,unlink,anchor,image,cleanup,|,insertdate,inserttime,preview,|,forecolor,backcolor,'.
							'|tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,fullscreen',
					'b3' => '',
					'b4' => ''
                );

        KMutil::update($def, $opt);

		$ret .= '
		<script type="text/javascript">
        tinyMCE.init({
                // General options
                mode : "exact",
                elements : "'.$form.'_'.$name.'_i0",

                theme : "advanced",
                relative_urls : 0,
                plugins : "imagemanager,filemanager,safari,pagebreak,style,layer,table,save,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                // Theme options
                theme_advanced_buttons1 : "'.$def['b1'].'",
                theme_advanced_buttons2 : "'.$def['b2'].'",
                theme_advanced_buttons3 : "'.$def['b3'].'",
                theme_advanced_buttons4 : "'.$def['b4'].'",

                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                content_css : "'.$def['css'].'",

                // Drop lists for link/image/media/template dialogs
                template_external_list_url : "/TinyMCE/examples/lists/template_list.js",
                external_link_list_url     : "/TinyMCE/examples/lists/link_list.js",
                external_image_list_url    : "/TinyMCE/examples/lists/image_list.js",
                media_external_list_url    : "/TinyMCE/examples/lists/media_list.js",

                // Replace values for the template plugin
                template_replace_values : {
                        username : "Some User",
                        staffid : "991234"
                }
        });
</script>';

			return $ret;

		}

		function js_re($tag = false)
		{
			self::js('kmin.re');

			/*
			if(!(KMlang::multi()))
				return '';

			$v = array();
			foreach(KMlang::$lang_names as $l)
				$v[] = "'$l'";

			$ret = '
                if(!kmin)kmin=new Object();
                if(!kmin.re)kmin.re=new Object();
                kmin.re.ls = new Array('.implode(', ', $v).');'.LF;
						
			$v = array();
			foreach(KMlang::$langs as $l)
				$v[] = "'input-$l'";

			$ret .= 'kmin.re.cs = new Array('.implode(', ', $v).');'.LF;

			return $tag ? '<script language="javascript">'.LF.'<!--'.LF.$ret.'-->'.LF.'</script>'.LF : $ret;
			*/
		}

		function js_tiny($tag = false)
		{
			self::js('tiny');
			/*
			$ret = WEB_SITE.'/TinyMCE/jscripts/tiny_mce/tiny_mce.js';
			return $tag ? '<script type="text/javascript" src="'.$ret.'"></script>'.LF : $ret;
			*/
		}

		function re_input($id, $fid, $val="", $input=true, $init=0, $style=' style="width:100%;"', $prejs = '')
		{
            if(is_array($input))
            {
                $def = array(
                            'input' => true,
                            'init' => 0,
                            'html' => 'style="width:100%"',
                            'prejs' => ''
                        );
                KMutil::update($def, $input);
                $init  = $def['init'];
                $input = $def['input'];
                $style = $def['style'];
                $prejs = $def['prejs'];
				$style = $def['html'];
            }

            self::js('kmin.re');
            //self::js('km18');

			$fid = $fid.'_'.$id;

			if(count(KMlang::$langs) < 2)
			{
				if($input)
					return self::input($id, $val, '', $style.' id="'.$fid.'_i0" ');
				else
					return self::textarea($id, $val, '20', $style.' id="'.$fid.'_i0" ');
			}


			$ls = KMlang::$langs;
			$c = count($ls);
			$arr = KMlang::values_map($val);

			$ret = '<div class="reinputs" id="'.$fid.'">&nbsp;';

			$i = 0;
			foreach($ls as $l)
				$ret .=  '<span class="re_'.$l.'" id="'.$fid.'_s'.($i++).'"></span>'.LF;
			$ret .= '<br>'.LF;

			$ru    = array_shift($ls);
			$ruval = array_shift($arr);

			if($input)
				$ret .= '<input type="text" id="'.$fid.'_i0" name="'.$id.'_'.$ru.'" value="'.self::val($ruval).'" '.$style.' />'.LF;
			else
				$ret .= '<textarea id="'.$fid.'_i0" name="'.$id.'_'.$ru.'" '.$style.'>'.self::val($ruval).'</textarea>'.LF;

			$i = 1;
			foreach($ls as $l)
				$ret .= '<input type="hidden" id="'.$fid.'_i'.($i++).'" name="'.$id.'_'.$l.'" value="'.self::val($arr[$l]).'" />'.LF;

			$ret .= '
			</div>		
            '.$prejs.'
			<script language="javascript">
			<!--
				kmin.re.go("'.$fid.'", '.$init.');
			-->
			</script>
			';

			return $ret;
		}

		function re_tiny($id, $fid, $values, $row="20", $init=0, $css='/css/style.css')
		{
            if(is_array($row))
            {
                $def = array(
                            'row' => '20',
                            'init' => '0',
                            'css'  => '/css/style.css',
							'style' => 'width:100%;',
                        );
                KMutil::update($def, $row);
            }
            else
            {
                $def = array(
                        'row' => $row,
                        'init' => $init,
                        'css' => $css,
						'style' => 'width:100%;',
                    );
            }

			return self::re_input($id, $fid, $values, false, $def['init'], 'rows="'.$def['row'].'" style="'.$def['style'].'"').self::tiny($id, $fid, $def);
		}

		function re_panel()
		{
			if(count(KMlang::$langs) < 2)
				return '';

			$ret = '<div class="repanel"><span>';

			$i = 0;
			foreach(KMlang::$langs as $l)	
					$ret .= '<a href="javascript:km18_all('.($i++).');">'.$l.'</a></span>'.LF;

			return $ret.'</div>'.LF;
		}


		function _($tag)
		{
			return '</'.$tag.'>'.LF;
		}

		function form($id='', $action='', $method='POST', $files=false, $ops='')
		{
			$id = $id ? ' id="'.$id.'"' : '';
			$action = $action ? ' action="'.$action.'"' : '';
			$files  = $files ? ' enctype="multipart/form-data"' : '';
			if($ops)
				$ops = " $ops";

			return '<form method="'.$method.'"'.$id.$action.$files.$ops.' >';
		}

		function ok($text, $name='', $ops='')
		{
			if($name)
				$name = ' name="'.$name.'"';
			if($ops)
				$ops = " $ops";

			return '<input type="submit" value="'.$text.'"'.$name.$ops.' />'.LF;
		}

		function cancel($text, $name='', $ops='')
		{
			if($name)
				$name = ' name="'.$name.'"';
			if($ops)
				$ops = " $ops";

			return '<input type="reset" value="'.$text.'" />'.LF;
		}




        // Constructions =======================================================

        function spoller($id, $title, $inner = null)
        {
            self::js('spoller');
            self::css('spoller');

            $ret = '<div id="'.$id.'" class="km-spoller">
                    <span id="'.$id.'_btn" class="km-spoller-btn" onclick="km_spoller_sw(km_spoller_sw(\''.$id.'\'));">+</span>'.
                    '<span id="'.$id.'_title" class="km-spoller-title">'.$title.'</span><br>'.
                    '<div id="'.$id.'_inner" class="km-spoller-inner">';
            
            if(isset($inner))
                $ret .= $inner.self::_spoller();
           
            return $ret; 
        }
         
        function _spoller()
        {
            return '</div></div>'.LF;
        }        


        // $id - html id prefix
        // $help - help id, relative path in kmin/docs/help/{lang}/ directory
        // $ops - ...
		function help($id, $help, $ops, $inner=false)
		{
			if(!is_array($ops))
			{
				$w = intval($ops);
				if($w)
					$ops = array('width' => $w);
				else
					$ops = array();
			}

			$t = array(
						"activation: 'click'",
						"sticky: true",
						"closePosition: 'title'",
						"closeText: '<img src=\"".WEB_ICON."/close.png\" />'",
						);
			foreach($ops as $n => $v)
				$t[] = "$n: $v";

			// Direct cluetip instead of document.ready()
			if($inner)
			{
				$before = '';
				$after = '';
			}	
			else
			{
				$before = '$(document).ready(function(){ ';
				$after = ' });';
			}

			self::js('jq.tip');
			return  '<a id="'.$id.'" href="#" rel="'.WEB_TASK.'/help.php?help='.urlencode($help).'"
						title="'.MSG_HELP.'">'.
						'<img src="'.WEB_ICON.'/question.png" width="16"></a>'.
						self::script($before.'$("#'.$id.'").cluetip({'.implode(', ', $t).'});'.$after);
		}

        // =====================================================================
	}


?>
