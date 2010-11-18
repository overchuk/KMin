<?

KM::ns('html');
KM::ns('class');
KMclass::uses('cell');

class KM_cell_cb extends KM_cell
{

    function init($in)
    {
        $def = array(   
                        'cbname' => 'ids',
                        'html' => array('th' => 'style="width:20px;"', 'td' => 'style="width:20px;"')
                    );

        KMutil::update($def, $in);
        parent::init($def);
    }
    

    function th($id, $fid)
    {
        KMhtml::js('jquery');

        $cbid   = $this->data['cbid']; 
        $cbname = $this->data['cbname']; 

        $id = $fid.'_'.$id;

        return '
<input type="checkbox" id="'.$id.'_all" />
<script language="javascript">
<!--
    $(document).ready(function(){

    $("#'.$id.'_all").change(function(){

        if(this.checked)
            $(".' . $id . '_class").attr("checked", "checked");
        else
            $(".' . $id . '_class").removeAttr("checked");

    });});
-->
</script>
';

    }

    function td($id, $fid, $row)
    {
        return '<input type="checkbox" class="'.$fid.'_'.$id.'_class" name="'.$this->data['cbname'].'[]" value="'.$row['id'].'"  />';
    }


}

?>
