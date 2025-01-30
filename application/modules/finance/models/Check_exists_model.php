<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Check_exists_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}

	function check_deduction($code)
	{
		$total_exists = 0;
		$total_exists = $this->db->where('deductionCode',$code)->from('tblempdeductloan')->count_all_results();
		$total_exists = $this->db->where('deductionCode',$code)->from('tblempdeductloanconadjust')->count_all_results();
		$total_exists = $this->db->where('deductionCode',$code)->from('tblempdeductionremit')->count_all_results();
		$total_exists = $this->db->where('deductionCode',$code)->from('tblempdeductions')->count_all_results();
		return $total_exists;
	}
	
	function check_agency($code)
	{
		$total_exists = 0;
		$total_exists = $this->db->where('deductionGroupCode',$code)->from('tbldeduction')->count_all_results();
		return $total_exists;
	}

	function check_income($code)
	{
		$total_exists = 0;
		$total_exists = $this->db->where('incomeCode',$code)->from('tblempaddincome')->count_all_results();
		$total_exists = $this->db->where('incomeCode',$code)->from('tblempbenefits')->count_all_results();
		$total_exists = $this->db->where('incomeCode',$code)->from('tblempincome')->count_all_results();
		$total_exists = $this->db->where('incomeCode',$code)->from('tblempincomeadjust')->count_all_results();
		$total_exists = $this->db->where('incomeCode',$code)->from('tblempmealdetails')->count_all_results();
		return $total_exists;
	}

	function check_payroll_process($code)
	{
		$total_exists = 0;
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblpayrollprocess')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblcomputationinstance')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblempdeductionremit')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblempincomeadjust')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblnonpermcomputationinstance')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblotcomputationinstance')->count_all_results();
		$total_exists = $this->db->where('appointmentCode',$code)->from('tblprocessedemployees')->count_all_results();
		return $total_exists;
	}

	function check_project_code($code)
	{
		$total_exists = 0;
		$total_exists = $this->db->where('projectCode',$code)->from('tblprocessedemployees')->count_all_results();
		$total_exists = $this->db->where('projectCode',$code)->from('tblpayrollgroup')->count_all_results();
		$total_exists = $this->db->where('projectCode',$code)->from('tblprocessedpayrollgroup')->count_all_results();
		$total_exists = $this->db->where('projectCode',$code)->from('tblprocessedproject')->count_all_results();
		return $total_exists;
	}



}
/* End of file Check_exists_model.php */
/* Location: ./application/modules/finance/models/Check_exists_model.php */