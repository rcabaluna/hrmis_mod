<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Adjustments_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	# BEGIN INCOME
	function addAdj_income($arrData)
	{
		$this->db->insert('tblempincomeadjust', $arrData);
		return $this->db->insert_id();
	}

	function editAdj_income($arrData, $code)
	{
		$this->db->where('code',$code);
		$this->db->update('tblempincomeadjust', $arrData);
		return $this->db->affected_rows();
	}

	public function delAdj_income($code)
	{
		$this->db->where('code', $code);
		$this->db->delete('tblempincomeadjust');
		return $this->db->affected_rows(); 
	}

	function getIncome($empnumber, $month=0, $yr=0, $period=0)
	{
		$where = "empNumber = '".$empnumber."'";
		if($month > 0): $where .= " AND (adjustMonth = '".$month."' OR adjustMonth = '".sprintf("%02d", $month)."')"; endif;
		if($yr > 0): $where .= " AND adjustYear = '".$yr."'"; endif;
		if($period > 0): $where .= " AND adjustPeriod = '".$period."'"; endif;

		$res = $this->db->query("SELECT tblempincomeadjust.*, tblincome.incomeDesc FROM tblempincomeadjust
									LEFT JOIN tblincome ON tblincome.incomeCode = tblempincomeadjust.incomeCode
									WHERE $where
									ORDER BY adjustYear DESC, adjustMonth DESC")->result_array();
		return $res;

	}
	# END INCOME

	# BEGIN DEDUCTIOM
	function addAdj_deduction($arrData)
	{
		$this->db->insert('tblempdeductloanconadjust', $arrData);
		return $this->db->insert_id();
	}

	function editAdj_deduction($arrData, $code)
	{
		$this->db->where('code',$code);
		$this->db->update('tblempdeductloanconadjust', $arrData);
		return $this->db->affected_rows();
	}

	public function delAdj_deduction($code)
	{
		$this->db->where('code', $code);
		$this->db->delete('tblempdeductloanconadjust');
		return $this->db->affected_rows(); 
	}

	function getDeductions($empnumber, $month=0, $yr=0, $period=0)
	{
		$where = "empNumber = '".$empnumber."'";
		if($month > 0): $where .= " AND (adjustMonth = '".$month."' OR adjustMonth = '".sprintf("%02d", $month)."')"; endif;
		if($yr > 0): $where .= " AND adjustYear = '".$yr."'"; endif;
		if($period > 0): $where .= " AND adjustPeriod = '".$period."'"; endif;

		return $this->db->query("SELECT * FROM `tblempdeductloanconadjust`
									LEFT JOIN `tbldeduction` ON `tbldeduction`.`deductionCode` = `tblempdeductloanconadjust`.`deductionCode`
									WHERE $where
									ORDER BY `adjustYear` DESC, `adjustMonth` DESC")->result_array();
		
	}
	# END DEDUCTION
	

}

/* End of file Adjustments_model.php */
/* Location: ./application/modules/finance/models/Adjustments_model.php */