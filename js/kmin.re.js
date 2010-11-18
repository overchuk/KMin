if(!kmin)var kmin=new Object;
if(!kmin.re)kmin.re=new Object();
kmin.re.st = new Object();
kmin.re.re = new Array();

kmin.re.fill = function(fid){
	var t = kmin.re.re[ fid ];
	if(!t)
		t = 0;

	var i;
	for(i=0; i<kmin.re.ls.length; i++)
	{
		var s = document.getElementById(fid + '_s' + i);
		if(i != t)
			s.innerHTML = '[<a href="javascript:kmin.re.go(\''+fid+'\', '+i+');">'+kmin.re.ls[i]+'</a>]';
		else
			s.innerHTML = '[<b>'+kmin.re.ls[i]+'</b>]';
	}

	document.getElementById(fid+'_i0').className = kmin.re.cs[t];
}

kmin.re.go=function(fid, n){
	var t = kmin.re.re[ fid ];
	if(!t)
		t = 0;

	if(n != t)
	{
		var i0 = document.getElementById(fid + '_i0');
		var i1 = document.getElementById(fid + '_i' + t);
		var mce;

			
		if(tinyMCE)
		{
			mce = tinyMCE.get(fid + '_i0');
			if(mce)
				i0.value = mce.getContent();
		}


		if( n == 0)
		{
			var tn = i0.name;
			var tv = i0.value;
			
			i0.name  = i1.name;
			i0.value = i1.value;

			i1.name  = tn;
			i1.value = tv;
		}
		else
		{
			var i2 = document.getElementById(fid + '_i' + n);
			var tn = i2.name;
			var tv = i2.value;

			i2.name  = i1.name;
			i2.value = i1.value;

			i1.name  = i0.name;
			i1.value = i0.value;

			i0.name  = tn;
			i0.value = tv;
		}

		if(mce)
			mce.setContent(i0.value);

		kmin.re.re[ fid ] = n;
	}

	kmin.re.fill(fid);
}


kmin.re.all=function(n){
	$('.reinputs').each( function(){ kmin.re.go(this.id, n); } ); 
}
