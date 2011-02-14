<?

KM::ns('lang');

class KMprops
{


static $types = array();

function types()
{
	return self::$types;
}


function ps2form($fid, &$ps, $row)
{
	KM::ns('class');
	KM::ns('html');

	if(!is_array($row))
		$row = array();

	foreach($ps as $n => $v)
	{
		$t = KMclass::obj($v['type']);
		//$ps[$n]['type'] = $t;

		$scr .= 'function '.$fid.'_'.$n.'_check(){'.LF.$t->js_check($fid, $n, $v).LF.'}'.LF;

		$war = $v['warning'];
		if(!$war)
			$war = MSG_INVALID_VALUE.': '.$v['title'];
		
		$ons .= 'if( ! '.$fid.'_'.$n.'_check() ) {
	
						alert("'.KMhtml::val($war).'");	
						'.$t->js_focus($fid, $n, $v).'
						return false;
					}'.LF;
			
		$tab .= $t->tr($fid, $n, $v, $row);
	}

	echo KMhtml::script($scr.'
		function '.$fid.'_on_submit(){
					'.$ons.'		
					return true;		
				}'.LF);

	echo KMhtml::tag('table', $tab, 'class="props"');
}


function form2value($ps, $post = null)
{
	KM::ns('class');

	$row = array();
	foreach($ps as $n => $v)
	{
		$t = KMclass::obj($v['type']);
		if(!$t->php_value($n, $row, $post))
			return $n;
	}

	return $row;
}


/*

 Draw form for editing properties set $ps
 or processing this form.
 If $_POST present, processed data, change $ps 
 and return true

*/
function edit($fid, &$ps)
{
	if($_POST['task'] == $fid.'_props_edit')
	{
		// XXX
		$ps = $_POST;
		return true;
	}
	
	KM::ns('html');
	KMhtml::js('kmin.rowedit');
	KMhtml::js('kmin.validator');

	echo KMhtml::script('

	function '.$fid.'__error(msg)
	{
		alert(msg);
		document.getElementById("'.$fid.'__name").focus();
	}

	function '.$fid.'__check(t,v)
	{
		return t.value != v;
	}

	function '.$fid.'__add(name, type, text)
	{
		if(! kmin.validator.vstr(name, 1, 32, "^[a-z0-9]*$") )
		{
			'.$fid.'__error("'.MSG_INVALID_VALUE.'"); 
			return false;
		}

		var test = name;	
		var bad  = false;
		var t = $("input.'.$fid.'__form_name").each(function(){
				if(this.value == test)
				{
					bad = true;
					return false;
				}
				return true;
			});
		if( bad )
		{
			'.$fid.'__error("'.MSG_ALREADY_EXISTS.': "+name); 
			return false;
		}

		h  = \'<div style="margin:0px;padding:0px;width:50%;float:left;background-color:#F0FFF0"><div style="padding:5px;">\';
		h += \'<input type="hidden" class="'.$fid.'__form_name" name="'.$fid.'__fname[]" value="\'+name+\'" />\';
		h += \'<strong>\'+name+\' - \'+text+\'</strong><br>\';
		h += \'<table cellspacing="0" cellpadding="3" border="0" width="100%"><tr><td width="10">'.MSG_TITLE.':</td><td>\';
		h += \' <input type="text" style="width:100%" name="'.$fid.'__ftitle_\'+name+\'" value="" /></td></tr><tr><td width="10">\';
		h += \''.MSG_DESCRIPTION.':</td><td><input type="text" name="'.$fid.'__fdescr_\'+name+\'" value="" style="width:100%;" /></td></tr></table>\';
		h += \'</div></div><div \';
		h += \'style="margin:0px;padding:0px;width:50%;height:100%;float:left;">\'; 
		h += \'<div id="'.$fid.'__param_\'+name+\'" style="padding:5px;">&nbsp;</div></div>\';

		kmin.rowedit.add("'.$fid.'", h);

		$.get(kmin.def.web_root+"/task/prop/param.php?type="+type+"&prefix='.$fid.'__fp_"+name+"&lang="+kmin.def.lang+"&site="+kmin.def.web_site, 
				function(data){
					var n = name;
					$("#'.$fid.'__param_"+name).html(data);
				});
	} 
	
');

	echo MSG_INSERT.'&nbsp;'.MSG_PROPERTY.': <input type="text" id="'.$fid.'__name" value="" />&nbsp;&nbsp;'.
			KMhtml::combobox('', self::types(), null, ' id="'.$fid.'__type" ').'&nbsp;';

	echo '<button onclick="'.$fid.'__add(document.getElementById(\''.$fid.'__name\').value, '.
			'document.getElementById(\''.$fid.'__type\').value, '.
			'$(\'#'.$fid.'__type :selected\').text() );">';

	echo MSG_INSERT.'</button>'.
		KMhtml::help($fid.'__help_insert', 'props/edit', 600).'<hr>'.LF; 


	echo '<form method="POST" id="'.$fid.'"><input type="hidden" name="task" value="'.$fid.'_props_edit">
	<table id="'.$fid.'__table" width="100%" cellspacing="0" cellpadding="3" border="1"><tbody class="rowedit">'.LF;
	echo '</tbody></table>'.LF;	
	echo '<input type="submit" value="'.MSG_SAVE.'"></form><br>'.LF;


}


}	

?>
