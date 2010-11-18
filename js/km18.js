// Русский язык
var km18_st = new Object();
// var tinyMCE = null;

function km18_fill(fid)
{
	var t = km18_st[ fid ];
	if(!t)
		t = 0;

	var i;
	for(i=0; i<km18_ls.length; i++)
	{
		var s = document.getElementById(fid + '_s' + i);
		if(i != t)
			s.innerHTML = '[<a href="javascript:km18_go(\''+fid+'\', '+i+');">'+km18_ls[i]+'</a>]';
		else
			s.innerHTML = '[<b>'+km18_ls[i]+'</b>]';
	}

	document.getElementById(fid+'_i0').className = km18_cs[t];
}

function km18_go(fid, n)
{
	var t = km18_st[ fid ];
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

		km18_st[ fid ] = n;
	}

	km18_fill(fid);
}


function km18_all(n)
{
	$('.reinputs').each( function(){ km18_go(this.id, n); } ); 
}
