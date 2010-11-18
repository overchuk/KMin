function km_spoller_sw(id)
{
    var b = $('#'+id+'_btn');
    var i = $('#'+id+'_inner');

    if(b.html() == '-')
    {
        b.html('+');
        i.css('display', 'none');
    }
    else
    {
        b.html('-');
        i.css('display', 'block');
    }

}
