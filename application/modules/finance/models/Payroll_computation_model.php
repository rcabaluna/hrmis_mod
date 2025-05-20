<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Payroll_computation_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	function getData($code='')
	{
		if($code==''):
			return $this->db->order_by('RATAAmount','DESC')->get('tblrata')->result_array();
		else:
			return $this->db->get_where('tblrata', array('RATACode' => $code))->result_array();
		endif;
	}

}
/* End of file Rata_model.php */
/* Location: ./application/modules/finance/models/Rata_model.php */