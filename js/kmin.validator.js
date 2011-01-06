if(!kmin)var kmin=new Object();
if(!kmin.validator)kmin.validator=new Object();

kmin.validator.num=function(id,min,max){
    var i = document.getElementById(id).value * 1.0;
	if(min != '')
	{
		min = min*1.0;
		if(i < min)
			return false;
	}

	if(max != '')
	{
		max = max*1.0;
		if(i > max)
			return false;
	}

    return true;
}

kmin.validator.str=function(id, min, max, mask){
    var s = new String( document.getElementById(id).value );
    var l = s.length;

	if(min != '')
	{
		min = min*1.0;
		if(l < min)
			return false;
	}

	if(max != '')
	{
		max = max*1.0;
		if(l > max)
			return false;
	}

	if(mask == '')
		return true;

    var r = new RegExp(mask);
    return r.test(s);
}

