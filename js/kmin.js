if(!tinyMCE)var tinyMCE=null;
if(!kmin)var kmin=new Object;
if(!kmin.sys)kmin.sys=new Object;

kmin.sys.draw = function(div,html)
{
    div1 = document.getElementById(div);
	div1.innerHTML = html;
    scripts = div1.getElementsByTagName('script');
    for (node_id in scripts){
        if(scripts[node_id].innerHTML){
            code = scripts[node_id].innerHTML;
            obj3 = document.createElement('script');
            obj3.language = "JavaScript";
            obj3.type = "text/JavaScript";
            obj3.innerHTML = code;
            parentObj = scripts[node_id].parentNode
            parentObj.removeChild(scripts[node_id]);
            parentObj.insertBefore(obj3, parentObj.firstChild);
        }
    }
}
