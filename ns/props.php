<?

KM::ns('lang');

class KMprops
{


static $types = array();

function types()
{
	return self::$types;
}

function type($type)
{
	return self::$types[ $type ];
}

function load_table($tab)
{
	$ret = array();
	$res = KMdb::query('SELECT * FROM `#__props` WHERE `tab`="'.KMdb::val($tab).'" ORDER BY `orde`');
	while($r = KMdb::fetch($res))
		$ret[$r['name']] = array(
				'title' => $r['title'],
				'descr' => $r['descr'],
				'type' => KMclass::create($r['type'], $r['data'])
			);
	return $ret;
}

function save_table($tab, &$ps)
{
	KMdb::query('DELETE FROM `#__props` WHERE `tab`="'.KMdb::val($tab).'"');
	$ord = 0;
	foreach($ps as $it => $p)
	{
		$t = KMclass::obj($p['type']);
		$row = array(
				'tab' => $tab,
				'name' => $it,
				'title' => $p['title'],
				'descr' => $p['descr'],
				'type' => $t->name(),
				'data' => $t->str(),
				'orde' => ($ord++)
		);
	
		KMdb::insert('props', KMdb::vals($row));
	}
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
function edit($fid, &$ps, &$ret)
{

	// Processing editor result
	if($_POST['task'] == $fid.'_props_edit')
	{
		$ret = array();
		foreach($_POST[$fid.'__fname'] as $it)
		{
			// Create type, and load type parameters from POST
			$t = KMclass::create($_POST[$fid.'__ftype_'.$it]);
			$t->admin_load($fid.'__fp_'.$it);
			$ret[$it] = array(
					'title' => $_POST[$fid.'__ftitle_'.$it],
					'descr' => $_POST[$fid.'__fdescr_'.$it],
					'type' => $t
				);
		}
		return true;
	}
	

	// Else, run editor
	KM::ns('html');
	KMhtml::css('rowedit');
	KMhtml::js('kmin.rowedit');
	KMhtml::js('kmin.validator');

	$ss = array();
	foreach($ps as $it => $p)
	{
		// XXX Same HTML as rowedit.js
		// It is necessary to unificate output methods.
	
		$t = KMclass::obj($p['type']);
		$ss[] = rawurlencode(str_replace("\n", ' ',
		'<div class="rwe-left"><div>
		<input type="hidden" class="'.$fid.'__form_name" name="'.$fid.'__fname[]" value="'.$it.'" />
		<input type="hidden" class="'.$fid.'__form_name" name="'.$fid.'__ftype_'.$it.'" value="'.$t->name().'" />
		<strong>'.$it.' - '.self::type($t->name()).'</strong><br>
		<table class="rwe-tabin" cellspacing="0" cellpadding="3" border="0" width="100%"><tr><td width="10">'.MSG_TITLE.':</td><td>
		<input type="text" style="width:100%" name="'.$fid.'__ftitle_'.$it.'" value="'.$p['title'].'" /></td></tr><tr><td width="10">
		'.MSG_DESCRIPTION.':</td><td><input type="text" name="'.$fid.'__fdescr_'.$it.'" value="'.$p['descr'].'" style="width:100%;" /></td></tr></table>
		</div></div><div class="rwe-right"> 
		<div id="'.$fid.'__param_'.$it.'">'.$t->admin_form($fid.'_'.$it.'_fp', true).'</div></div>'));
	}

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

		h  = \'<div class="rwe-left"><div>\';
		h += \'<input type="hidden" class="'.$fid.'__form_name" name="'.$fid.'__fname[]" value="\'+name+\'" />\';
		h += \'<input type="hidden" class="'.$fid.'__form_name" name="'.$fid.'__ftype_\'+name+\'" value="\'+type+\'" />\';
		h += \'<strong>\'+name+\' - \'+text+\'</strong><br>\';
		h += \'<table class="rwe-tabin" cellspacing="0" cellpadding="3" border="0" width="100%"><tr><td width="10">'.MSG_TITLE.':</td><td>\';
		h += \' <input type="text" style="width:100%" name="'.$fid.'__ftitle_\'+name+\'" value="" /></td></tr><tr><td width="10">\';
		h += \''.MSG_DESCRIPTION.':</td><td><input type="text" name="'.$fid.'__fdescr_\'+name+\'" value="" style="width:100%;" /></td></tr></table>\';
		h += \'</div></div><div \';
		h += \'class="rwe-right">\'; 
		h += \'<div id="'.$fid.'__param_\'+name+\'">&nbsp;</div></div>\';

		kmin.rowedit.add("'.$fid.'", h);

		$.get(kmin.def.web_root+"/task/prop/param.php?type="+type+"&prefix='.$fid.'__fp_"+name+"&lang="+kmin.def.lang+"&site="+kmin.def.web_site, 
				function(data){
					var n = name;
					$("#'.$fid.'__param_"+name).html(data);
				});
	} 

	$(document).ready(function(){
	
		kmin.rowedit.add("'.$fid.'", decodeURIComponent("'.implode('"));
		kmin.rowedit.add("'.$fid.'", decodeURIComponent("',$ss).'"));	
	
	});
	
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
