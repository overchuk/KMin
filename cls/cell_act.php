<?

KM::ns('class');
KMclass::uses('cell');


class KM_cell_act extends KM_cell
{


    function td($id, $fid, $row)
    {
		$ret = '';

		if(is_array($this->data['list']))
			foreach($this->data['list'] as $name => $val)
			{
				if(!is_object($this->data['objs'][$name]))
					$this->data['objs'][$name] = KMclass::obj($this->data['list'][$name]);
				
				$action = $this->data['objs'][$name];
				$ret .= $action->td($name, $id, $fid, $row).'&nbsp;';
			}

        return $ret;
    }

}

?>
