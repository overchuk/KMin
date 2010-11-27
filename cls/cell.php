<?

KM::ns('class');
KMclass::uses();

class KM_cell extends KM_
{


    function th($id, $fid)
    {
        $ret = $this->data['title'];
        return $ret ? $ret : $id;
    }


    function td($id, $fid, $row)
    {
        $txt = KMutil::format( $this->data['text'], $row );

        if($this->data['link'])
        {
            $u = KMurl::create($this->data['link'], $row);
            $txt = '<a href="'.$u.'">'.$txt.'</a>';
        }

		if($this->data['nobr'])
			return "<nobr>$txt</nobr>";
		else
	        return $txt;
    }

}


?>
