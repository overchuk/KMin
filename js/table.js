
function km_table_checkall(id, cb)
{
    if(cb.checked)
        $('.'+id+'-checkbox').attr('checked', 'checked');
    else
        $('.'+id+'-checkbox').removeAttr('checked');
}

function km_table_post(id, iid, task)
{
    var f = document.getElementById(id+'_form');
    var t = document.getElementById(id+'_task');
    var i = document.getElementById(id+'_id');

    i.value = iid;
    t.value = task;
    f.submit();
}
