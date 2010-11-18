if(!kmin)var kmin=new Object;
if(!kmin.store)kmin.store=new Object;

kmin.store.hash = new Array;
kmin.store.url  = '/kmin/task/store.php';
kmin.store.set = function(name, value){ kmin.store.hash[name] = value; }
kmin.store.get = function(name){ return kmin.store.hash[name]; }


