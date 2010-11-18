<?

	class KMhtml
	{
		function val($s)
		{
			return htmlspecialchars($s);
		}

		function img($src, $alt, $ops='', $title='')
		{
			if($ops)
				$ops = " $ops";

			if(!$title)
				$title = $alt;


			return '<img src="'.$src.'" alt="'.$alt.'" title="'.$title.'"'.$ops.' />';
		}

		function script($inner, $opt='', $lang='javascript')
		{
			if(!$inner)
				return '';

			if($opt)
				$opt = " $opt";

			return '<script language="'.$lang.'"'.$opt.'>'.LF.'<!--'.LF.$inner.LF.'-->'.LF.'</script>'.LF;
		}

		function tag($name, $inner, $attr='', $lf=true)
		{
			return '<'.$name.' '.$attr.'>'.$inner.'</'.$name.'>'. ($ls ? LF : '');
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


		function input($name, $value, $max='', $ops='')
		{
			if($max)
				$max = ' size="'.$max.'"';

			if($ops)
				$ops = " $ops";

			return '<input type="text" name="'.$name.'" value="'.self::val($value).'"'.$ops.$max.'>';
		}

		function textarea($name, $value, $rows="10", $ops='')
		{
			if($ops)
				$ops = " $ops";

			return '<textarea name="'.$name.'" rows="'.$rows.'"'.$ops.'>'.self::val($value).'</textarea>';
		}


		function checkbox($name, $value, $ops='')
		{
			if($ops)
				$ops = " $ops";
			$chk = ($value ? ' checked="checked"' : '');
			return '<input type="checkbox"'.$ops.$chk.' />';
		}

		function combobox($name, $hash, $value, $ops='')
		{
			if($ops)
				$ops = " $ops";

			foreach($hash as $n => $v)
			{
				$sel = ($n == $value) ? ' selected="selected"' : '';
				$ret .= self::tag('option', $v, $sel.' value="'.$n.'"');
			}	

			return self::tag('select', $ret, 'value="'.$value.'"'.$ops);
		}

		function tiny($name, $form)
		{
		$ret .= '
		<script type="text/javascript">
        tinyMCE.init({
                // General options
                mode : "exact",
                elements : "'.$form.'_'.$name.'_i0",

                theme : "advanced",
                relative_urls : 0,
                plugins : "imagemanager,filemanager,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                // Theme options
                theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
                theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,

                content_css : "/css/style.css",

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
			if(!(KMlang::multi()))
				return '';

			$v = array();
			foreach(KMlang::$lang_names as $l)
				$v[] = "'$l'";

			$ret = 'var km18_ls = new Array('.implode(', ', $v).');'.LF;
						
			$v = array();
			foreach(KMlang::$langs as $l)
				$v[] = "'input-$l'";

			$ret .= 'var km18_cs = new Array('.implode(', ', $v).');'.LF;

			return $tag ? '<script language="javascript">'.LF.'<!--'.LF.$ret.'-->'.LF.'</script>'.LF : $ret;
		}

		function js_tiny($tag = false)
		{
			$ret = WEB_ROOT.'/TinyMCE/jscripts/tiny_mce/tiny_mce.js';
			return $tag ? '<script type="text/javascript" src="'.$ret.'"></script>'.LF : $ret;
		}

		function re_input($id, $fid, $val="", $input=true, $init=0, $style=' style="width:100%;"')
		{
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
			<script language="javascript">
			<!--
				km18_go("'.$fid.'", '.$init.');
			-->
			</script>
			';

			return $ret;
		}

		function re_tiny($id, $fid, $values, $row="20", $init=0)
		{
			return self::re_input($id, $fid, $values, false, $init, 'row="'.$row.'" style="width:100%;"') . self::tiny($id, $fid);
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
			echo '</'.$tag.'>'.LF;
		}

		function form($file=false, $action='', $method='POST', $ops='')
		{
			$action = $action ? ' action="'.$action.'"' : '';
			$files  = $files ? ' enctype="multipart/form-data"' : '';
			if($ops)
				$ops = " $ops";

			return '<form method="'.$method.'"'.$action.$files.$ops.' >';
		}

		function ok($text, $name='', $ops='')
		{
			if($name)
				$name = ' name="'.$name.'"';
			if($ops)
				$ops = " $ops";

			return '<input type="submit" value="'.$text.'" />'.LF;
		}

		function cancel($text, $name='', $ops='')
		{
			if($name)
				$name = ' name="'.$name.'"';
			if($ops)
				$ops = " $ops";

			return '<input type="reset" value="'.$text.'" />'.LF;
		}


	}


?>
