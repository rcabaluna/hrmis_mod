<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Longevity_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	function getLongevityFactor()
	{
		$payrollProc = $this->db->get_where('tblpayrollprocess', array('appointmentCode' => longeAppt()))->result_array();
		$payrollProc = "'".str_replace(",", "','", $payrollProc[0]['processWith'])."'";

		$empLonge = $this->db->join("tblempposition", "tblemppersonal.empNumber = tblempposition.empNumber", "inner")
									->where("tblempposition.statusOfAppointment", "In-Service")
									->where("(tblempposition.detailedfrom = 2 OR tblempposition.detailedfrom = 0)")
									->where("tblempposition.appointmentCode IN (".$payrollProc.")")
									->get("tblemppersonal")
									->result_array();
		$arrLongevity = array();
		$no=1;
		foreach($empLonge as $key => $longe):
			$fdayAgncy = $longe['firstDayAgency'];
			$fyrAgncy = date('Y', strtotime($fdayAgncy));
			$difYear = date('Y') - $fyrAgncy;
			$dateofIncrease = date('Y').'-'.date('m-d', strtotime($fdayAgncy));
			
			if(in_array($difYear, array('5','10','15','20','25','30','35','40')) && $difYear > $longe['longiFactor']):
				$longe['difYear'] = $difYear;
				$longe['dateofIncrease'] = $dateofIncrease;
				array_push($arrLongevity, $longe);
			endif;
		endforeach;
		return $arrLongevity;
	}

	function getLongevity($empid='')
	{
		return $this->db->get_where('tblemplongevity', array('empNumber' => $empid))->result_array();
	}

	function getLongevitySum($empid='')
	{
		$this->db->select('SUM(longiPay) AS longiPay');
		$res = $this->db->get_where('tblemplongevity', array('empNumber' => $empid))->result_array();
		return count($res) > 0 ? $res[0]['longiPay'] : 0;
	}

	function addLongevity($arrData)
	{
		$this->db->insert('tblemplongevity', $arrData);
		return $this->db->insert_id();
	}

	function editLongevity($arrData, $empid, $id)
	{
		$this->db->where('id',$id);
		$this->db->where('empNumber',$empid);
		$this->db->update('tblemplongevity', $arrData);
		return $this->db->affected_rows();
	}

	public function delLongevity($empid, $id)
	{
		$this->db->where('id',$id);
		$this->db->where('empNumber',$empid);
		$this->db->delete('tblemplongevity');
		return $this->db->affected_rows(); 
	}


}
/* End of file Longevity_model.php */
/* Location: ./application/modules/finance/models/Longevity_model.php */