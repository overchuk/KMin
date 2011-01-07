if(!kmin)var kmin=new Object();
if(!kmin.rowedit)kmin.rowedit=new Object();

kmin.rowedit.size = function(id){
	return $('#'+id+'__table tr').size();
}

kmin.rowedit.fill = function(id,sz){
		for(i=0; i<sz; i++)
		{
			var c = document.getElementById(id+'__bar_'+i);
			var h = '';
			if(i>0)
				h += '<a href="#" onclick="kmin.rowedit.up(\''+id+'\','+i+');">'+
				'<img border="0" width="16" src="'+kmin.def.web_icon+'/up.png" /></a>';
			else
				h += '<img border="0" width="16" src="'+kmin.def.web_icon+'/s.gif" />';

			if((i+1)<sz)
				h += '<a href="#" onclick="kmin.rowedit.down(\''+id+'\','+i+');">'+
				'<img border="0" width="16" src="'+kmin.def.web_icon+'/down.png" /></a>';
			else
				h += '<img border="0" width="16" src="'+kmin.def.web_icon+'/s.gif" />';

		
			h += '<a href="#" onclick="kmin.rowedit.del(\''+id+'\','+i+');">'+
				'<img border="0" width="16" src="'+kmin.def.web_icon+'/del.png" /></a>';

			c.innerHTML = h;
		}
}

kmin.rowedit.add=function(id,html){
	var sz = kmin.rowedit.size(id);
	var t = document.getElementById(id + '__table');
	t.innerHTML += '<tr id="'+id+'__row_'+sz+'"><td width="*" id="'+id+'__cell_'+sz+'">'+html+'</td><td width="64" id="'+id+'__bar_'+sz+'"></td></tr>';
	sz++;
	kmin.rowedit.fill(id,sz);
	return sz;
}

kmin.rowedit.up=function(id,n){
	var c1 = document.getElementById(id+'__cell_'+n);
	var c2 = document.getElementById(id+'__cell_'+(n-1));
	var h = c1.innerHTML;
	c1.innerHTML = c2.innerHTML;
	c2.innerHTML = h;
}

kmin.rowedit.down=function(id,n){
	kmin.rowedit.up(id,n+1);
}

kmin.rowedit.del=function(id,n){
	var sz = kmin.rowedit.size(id);
	for(i = n; (i+1)<sz; i++)
		document.getElementById(id+'__cell_'+i).innerHTML = document.getElementById(id+'__cell_'+(i+1)).innerHTML;

	$('#'+id+'__row_'+(sz-1)).remove();
	sz--;
	kmin.rowedit.fill(id,sz);
	return sz;
}


