if(!kmin)var kmin=new Object();
if(!kmin.validator)kmin.validator=new Object();

kmin.validator.integer=function(id,min,max){
    var i = document.getElementById(id).value * 1.0;
    return !((i < min) || (i > max));
}


kmin.validator.mask=function(id, mask){
    var s = new String( document.getElementById(id).value );
    var r = new RegExp(mask);
    return s.match(r);
}

kmin.validator.str=function(id,min,max,mask){
    
    var s = new String( document.getElementById(id).value );
    var l = s.length;

    if(min && (l < min))
        return false;

    if(max && (l > max))
        return false;

}


