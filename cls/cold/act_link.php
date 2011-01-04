<?

KM::ns('class');
KMclass::uses('action');

class KM_act_link extends KM_action
{
    function js_pre()
    {
        
    }

    function a($id, $fid, $row)
    {
		$link = KMutil::format($this->data['link'], $row);
		$c = KMutil::format($this->data['confirm'], $row);
		$ret = 'href="'.$link.'"';
		if($c)	
			$ret .= ' onclick="return confirm(\''.$c.'\');"';

		return $ret;
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
