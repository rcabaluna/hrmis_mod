<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Payroll_process_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	function getData($process_id=0)
	{
		if($process_id == 0):
			return $this->db->get('tblprocess')->result_array();
		else:
			$res = $this->db->get_where('tblprocess',array('processID' => $process_id))->result_array();
			return !empty($res) ? $res[0] : array();
		endif;
	}

	function getemployee_income($process_id)
	{
		return $this->db->get_where('tblempincome',array('processID' => $process_id))->result_array();
	}
	function get_incomedesc($incomecode)
	{
		$res = $this->db->get_where('tblincome',array('incomecode' => $incomecode))->result_array();
		return count($res) > 0 ? $res[0]['incomeDesc'] : $incomecode;
	}

	function getemployee_deductionremit($process_id)
	{
		$this->db->join('tbldeduction', 'tbldeduction.deductionCode = tblempdeductionremit.deductionCode','left');
		return $this->db->get_where('tblempdeductionremit',array('processID' => $process_id))->result_array();
	}

	function get_rata_details($code='')
	{
		if($code != ''):
			return $this->db->get_where('tblrata',array('RATACode' => $code))->result_array();
		else:
			return $this->db->get('tblrata')->result_array();
		endif;
	}

	function get_process_code($process_id)
	{
		set_sql_mode();
		$deduction_codes = $this->db->group_by('deductionCode')->get_where('tblempdeductionremit',array('processID' => $process_id))->result_array();
		$income_codes = $this->db->group_by('incomeCode')->get_where('tblempincome',array('processID' => $process_id))->result_array();
		return array_filter(array_merge(array_column($deduction_codes,'deductionCode'),array_column($income_codes,'incomeCode')));
	}

	function get_process_by_appointment($appt,$month,$yr,$period=0)
	{
		if($period!=0):
			if($appt != 'P'){
				$this->db->where('tblprocess.period',$period);
			}
		endif;
		$process = $this->db->get_where('tblprocess',array('employeeAppoint' => $appt, 'processMonth' => ltrim($month,'0'), 'processYear' => $yr))->result_array();
		foreach($process as $key => $p):
			$process[$key]['codes'] = $this->get_process_code($p['processID']);
		endforeach;

		return $process;
	}

	function get_payroll_process($month='',$yr='',$appt='',$processid='')
	{
		if($month!=''):
			$this->db->where('processMonth',ltrim($month,'0'));
		endif;
		if($yr!=''):
			$this->db->where('processYear',$yr);
		endif;
		if($appt!=''):
			$this->db->where('employeeAppoint',$appt);
		endif;
		if($processid!=''):
			$this->db->where('processID',$processid);
		endif;
		$this->db->join('tblappointment','tblappointment.appointmentCode = tblprocess.employeeAppoint','left');
		$process = $this->db->get('tblprocess')->result_array();
		
		return $process;
	}

	# Add payroll process
	function add_payroll_process($arrData)
	{
		$this->db->insert('tblprocess', $arrData);
		return $this->db->insert_id();
	}

	# Add processed project
	function add_processed_project($arrData)
	{
		$this->db->insert('tblprocessedemployees', $arrData);
		return $this->db->insert_id();
	}

	# Add processed employees
	function add_processed_employees($arrData)
	{
		$this->db->insert('tblprocessedproject', $arrData);
		return $this->db->insert_id();
	}

	# Add processed pgroup
	function add_processed_pgroup($arrData)
	{
		$this->db->insert('tblprocessedpayrollgroup', $arrData);
		return $this->db->insert_id();
	}

	# delete payroll process
	function delete_payroll_process($month,$yr)
	{
		$this->db->delete('tblprocess', array('processMonth' => ltrim($month,'0'), 'processYear' => $yr));
	}

	function delete_payroll_process_byid($id)
	{
		$this->db->delete('tblprocess', array('processID' => $id));
	}

	function getall_process($month='all',$yr)
	{
		if($month == 'all'):
			$condition = array('processYear' => $yr);
		else:
			$condition = array('processMonth' => $month, 'processYear' => $yr);
		endif;

		$this->db->order_by('processDate');
		$this->db->join('tblappointment','tblappointment.appointmentCode = tblprocess.employeeAppoint','left');
		$process = $this->db->get_where('tblprocess', $condition)->result_array();

		foreach($process as $key => $proc):
			$process[$key]['details'] = implode(', ',$this->getprocess_details($proc['processID']));
		endforeach;
		
		return $process;
	}

	function getprocess_details($process_id)
	{
		$process_details = $this->db->distinct()->select('deductionCode')->get_where('tblempdeductionremit', array('processID' => $process_id))->result_array();
		return array_column($process_details,'deductionCode');
	}

	function edit_payroll_process($arrData,$procid)
	{
		$this->db->where('processID',$procid);
		$this->db->update('tblprocess', $arrData);
		return $this->db->affected_rows();
	}

	function getEmployees($appt_code,$empid='',$tblposition=0,$tblproject=0)
	{
		
		
		$payroll_process = $this->db->get_where('tblpayrollprocess', array('appointmentCode' => $appt_code))->result_array();
		
		if(count($payroll_process) > 0):
			$condition['statusOfAppointment'] = 'In-Service';
			$condition['payrollSwitch'] = 'Y';
			if($empid!=''):
				$condition['tblemppersonal.empNumber'] = $empid;
			endif;

			# Employees
			$this->db->select("tblemppersonal.empNumber,tblemppersonal.surname,tblemppersonal.firstname,tblemppersonal.middlename,tblemppersonal.middleInitial,tblemppersonal.nameExtension,tblempposition.payrollGroupCode,
							    tblempposition.authorizeSalary,tblempposition.actualSalary,tblempposition.hpFactor,tblempposition.RATACode,tblempposition.RATAVehicle,
							    tblempposition.schemeCode,tblempposition.payrollSwitch,tblempposition.positionCode,tblempposition.positionCode,tblempposition.appointmentCode,
							    tblempposition.officeCode,tblempposition.taxSwitch");
			$this->db->join('tblemppersonal', 'tblemppersonal.empNumber = tblempposition.empNumber');
			if($tblposition==1){
				$this->db->select("tblposition.positionAbb");
				$this->db->join('tblposition', 'tblempposition.positionCode = tblposition.positionCode');
			}
			if($tblproject==1){
				$this->db->select('tblproject.projectCode,tblproject.projectDesc,tblproject.projectOrder,tblpayrollgroup.payrollGroupName,tblpayrollgroup.payrollGroupOrder');
				$this->db->join('tblpayrollgroup', 'tblempposition.payrollGroupCode = tblpayrollgroup.payrollGroupCode');
				$this->db->join('tblproject', 'tblproject.projectCode = tblpayrollgroup.projectCode');
			}
			$this->db->where_in('appointmentCode', explode(',',$payroll_process[0]['processWith']));
			$employees = $this->db->get_where('tblempposition',$condition)->result_array();

			return $employees;
		endif;
	}

	function process_with($appt_code)
	{
		$res = $this->db->get_where('tblpayrollprocess', array('appointmentCode' => $appt_code))->result_array();
		return count($res) > 0 ? $res[0] : null;
	}

	function update_or($arrData,$processid,$deduction_code,$empid)
	{
		$this->db->where('processID',$processid);
		$this->db->where('deductionCode',$deduction_code);
		$this->db->where('empNumber',$empid);
		$this->db->update('tblempdeductionremit', $arrData);
		return $this->db->affected_rows();
	}

	function update_orby_remitt($arrData,$remitt_id)
	{
		$this->db->where('remitt_id',$remitt_id);
		$this->db->update('tblempdeductionremit', $arrData);
		return $this->db->affected_rows();
	}

	function get_orlist($processid='',$deduction_code='',$isnull=0)
	{
		$this->db->select('tblprocess.processID,tblprocess.empNumber,tblprocess.processMonth,tblprocess.processYear,tblprocess.employeeAppoint,tbldeduction.deductionDesc,tbldeduction.deductionCode,tblempdeductionremit.deductMonth,tblempdeductionremit.deductYear,tblempdeductionremit.orNumber,tblempdeductionremit.orDate,tblempdeductionremit.remitt_id');
		$this->db->join('tbldeduction','tbldeduction.deductionCode = tblempdeductionremit.deductionCode');
		$this->db->join('tblprocess','tblprocess.processID = tblempdeductionremit.processID');
		
		if($isnull == 1){
			$this->db->where('orNumber IS NULL', null, false);
			$this->db->where('orDate IS NULL', null, false);
		}else{
			$this->db->where('orNumber IS NOT NULL', null, false);
			$this->db->where('orDate IS NOT NULL', null, false);
		}
		
		if($processid!=''){
			$this->db->where('tblprocess.processID',$processid);
		}
		if($deduction_code!=''){
			$this->db->where('tbldeduction.deductionCode',$deduction_code);
		}

		$res = $this->db->get('tblempdeductionremit')->result_array();
		
		return $res;
	}

	function get_deduction_remit($remitt_id)
	{
		$res = $this->db->get_where('tblempdeductionremit', array('remitt_id' => $remitt_id))->result_array();
		return count($res) > 0 ? $res[0] : null;
	}


}
/* End of file Process_model.php */
/* Location: ./application/modules/finance/models/Process_model.php */