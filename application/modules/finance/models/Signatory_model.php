<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Signatory_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	function add($arrData)
	{
		$this->db->insert('tblsignatory', $arrData);
		return $this->db->insert_id();
	}

	function edit($arrData, $code)
	{
		$this->db->where('signatoryId',$code);
		$this->db->update('tblsignatory', $arrData);
		return $this->db->affected_rows();
	}

	public function delete($id)
	{
		$this->db->where('signatoryId', $id);
		$this->db->delete('tblsignatory');
		return $this->db->affected_rows(); 
	}

	function getSignatories($id)
	{
		if($id==''):
			return $this->db->order_by('signatory','ASC')->get('tblsignatory')->result_array();
		else:
			$result = $this->db->get_where('tblsignatory', array('signatoryId' => $id))->result_array();
			return count($result) > 0 ? $result[0] : array();
		endif;
	}	

	function getSignatoriesByModule($module)
	{
		return $this->db->get_where('tblsignatory', array('sig_module' => $module))->result_array();
	}
		
}
/* End of file Signatory_model.php */
/* Location: ./application/modules/finance/models/Signatory_model.php */