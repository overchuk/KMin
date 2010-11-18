<?

KM::ns('class');
KMclass::uses('action');

class KM_act_js extends KM_action
{
    function js_pre()
    {
        
    }

    function a($id, $fid, $row)
    {
		$js = KMutil::format($this->data['js'], $row);
		$c =  KMutil::format($this->data['confirm'], $row);
		if($c)
			$js = "if(confirm('$c')){ $js }";

		return 'href="javascript:'.$js.'"';
    }

    
    function td($act, $id, $fid, $row)
    {
        $txt = '<a title="'.$this->data['title'].'" '.$this->a($id, $fid, $row).'>';
        if($this->data['icon'])
        {
            if($this->data['icon_path'])
                $f =$this->data['icon_path'] .SL. $this->data['icon'];
            else
                $f = WEB_ICON.SL. $this->data['icon'];
        
            $txt .= '<img border="0" src="'.$f.'" alt="'.$this->data['title'].'" '.$this->data['html']['img'].'/>';
        }
        else
            $txt .= $this->data['title'];

        $txt .= '</a>';

		return $txt;
    }


}

?>
