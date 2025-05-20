<?php 
/** 
Purpose of file:    Model for PDS update
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pds_update_model extends CI_Model {

	var $table = 'tblemppersonal';
	var $tableid = 'empNumber';

	var $tableSchool = 'tblempschool';
	var $tableSchoolid = 'levelCode';

	var $tableEduc = 'tbleducationallevel';
	var $tableEducid = 'levelId';

	var $tableCourse = 'tblcourse';
	var $tableCourseid = 'courseCode';

	var $tableScholarship = 'tblscholarship';
	var $tableScholarshipid = 'id';

	var $tableTraining = 'tblemptraining';
	var $tableTrainingid = 'XtrainingCode';

	var $tableExam = 'tblexamtype';
	var $tableExamid = 'examId';

	var $tableWorkExp = 'tblservicerecord';
	var $tableWorkExpid = 'serviceRecID';
	
	var $tableAppoint = 'tblappointment';
	var $tableAppointid = 'appointmentId';

	var $tableSepCause = 'tblseparationcause';
	var $tableSepCauseid = 'separationCause';

	var $tableRef = 'tblempreference';
	var $tableRefid = 'refName';

	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}
	
	// getting data
	public function getDetails($strEmpNo="",$strSearch="",$strAppStatus="")
	{
		$this->db->select('tblemppersonal.*,tblempposition.*,tblposition.positionDesc,tblappointment.appointmentDesc');
		$where='';
		if($strEmpNo!="")
			$this->db->where('tblemppersonal.empNumber',$strEmpNo);
			//$where .= " AND tblemppersonal.empNumber='".$strEmpNo."'";
		if($strSearch!="")
			$this->db->where("(tblemppersonal.empNumber LIKE '%".$strSearch."%' OR surname LIKE '%".$strSearch."%' OR firstname LIKE '%".$strSearch."%' OR middlename LIKE '%".$strSearch."%')",NULL,FALSE);
			//$where .= " AND (tblemppersonal.empNumber LIKE '%".$strSearch."%' OR surname LIKE '%".$strSearch."%' OR firstname LIKE '%".$strSearch."%' OR middlename LIKE '%".$strSearch."%')";
		if($strAppStatus!="")
			$this->db->where('tblempposition.statusOfAppointment',$strAppStatus);
		else
			$this->db->where('tblempposition.statusOfAppointment','In-Service');
			//$where .= " AND tblempposition.statusOfAppointment='".$strAppStatus."'";
		$this->db->join('tblempposition','tblempposition.empNumber=tblemppersonal.empNumber','left')
		->join('tblposition','tblposition.positionCode=tblempposition.positionCode','left')
		->join('tblappointment','tblappointment.appointmentCode=tblempposition.appointmentCode','left')
		->order_by('surname,firstname,middlename','asc');
		$strSQL = " SELECT tblemppersonal.*,tblempposition.statusOfAppointment,tblempposition.appointmentCode,tblempposition.positionCode,tblposition.positionDesc FROM tblemppersonal						
					LEFT JOIN tblempposition ON tblempposition.empNumber=tblemppersonal.empNumber
					LEFT JOIN tblposition ON tblposition.positionCode=tblempposition.positionCode
					WHERE 1=1 
					$where
					ORDER BY surname,firstname,middlename
					";
		// echo $strSQL;exit(1);				
		//$objQuery = $this->db->query($strSQL);
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}	

	function getData($strEmpNumber = '')
	{		
		if($strEmpNumber != "")
		{
			$this->db->where($this->tableid,$strEmpNumber);
		}
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	function getEmployeeDetails($strEmpNo,$strSelect,$strTable,$strOrder="",$strJoinTable="",$strJoinString="",$strJoinType="")
	{
		if($strOrder!='')
			$this->db->order_by($strOrder);
		if($strJoinTable!='' && $strJoinString!='' && $strJoinType!='')
			$this->db->join($strJoinTable,$strJoinString,$strJoinType);
		if($strEmpNo!='')
			$this->db->where('empNumber',$strEmpNo);
		$this->db->select($strSelect);
		$rs = $this->db->get($strTable);
		return $rs->result_array();
	}

	function getEducData($intLevelId = '')
	{		
		if($intLevelId != "")
		{
			$this->db->where($this->tableEducid,$intLevelId);
		}
		$objQuery = $this->db->get($this->tableEduc);
		return $objQuery->result_array();	
	}
	function getEduc($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_EDUC.'.empNumber','left');
		$this->db->where(TABLE_EDUC.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_EDUC);
		return $objQuery->result_array();	
	}

	function getCourseData($strCourseCode = '')
	{		
		if($strCourseCode != "")
		{
			$this->db->where($this->tableCourseid,$strCourseCode);
		}
		$objQuery = $this->db->get($this->tableCourse);
		return $objQuery->result_array();	
	}

	function getScholarshipData($intScholarId = '')
	{		
		if($intScholarId != "")
		{
			$this->db->where($this->tableScholarshipid,$intScholarId);
		}
		$objQuery = $this->db->get($this->tableScholarship);
		return $objQuery->result_array();	
	}
	function getSchoolData($intEmpNum = '')
	{		
		if($intEmpNum != "")
		{
			$this->db->where($this->tableSchoolid,$intEmpNum);
		}
		$objQuery = $this->db->get($this->tableSchool);
		return $objQuery->result_array();	
	}

	function getTrainingData($strTableTraining = '')
	{		
		if($strTableTraining != "")
		{
			$this->db->where($this->tableTrainingid,$strTableTraining);
		}
		$objQuery = $this->db->get($this->tableTraining);
		return $objQuery->result_array();	
	}
	function getTraining($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_TRAINING.'.empNumber','left');
		$this->db->where(TABLE_TRAINING.'.empNumber',$strEmpNumber);
		$this->db->order_by('tblemptraining.'.$this->tableTrainingid,'ASC');
		$objQuery = $this->db->get(TABLE_TRAINING);
		return $objQuery->result_array();	
	}

	function getExamData($intExamId = '')
	{		
		if($intExamId != "")
		{
			$this->db->where($this->tableExamid,$intExamId);
			$this->db->order_by('tableExam'.$this->tableExamid,'ASC');
		}
		$objQuery = $this->db->get($this->tableExam);
		return $objQuery->result_array();	
	}
	function getExamination($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_EXAM.'.empNumber','left');
		$this->db->where(TABLE_EXAM.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_EXAM);
		return $objQuery->result_array();	
	}

	function getRefData($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_REFERENCE.'.empNumber','left');
		$this->db->where(TABLE_REFERENCE.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_REFERENCE);
		//echo $this->db->last_query();
		return $objQuery->result_array();	
	}

	function getVoluntary($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_VOLUNTARY.'.empNumber','left');
		$this->db->where(TABLE_VOLUNTARY.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_VOLUNTARY);
		return $objQuery->result_array();	
	}

	function getExpData($intSerId = '')
	{		
		if($intSerId != "")
		{
			$this->db->where($this->tableWorkExpid,$intSerId);
		}
		$objQuery = $this->db->get($this->tableWorkExp);
		return $objQuery->result_array();	
	}

	function getExperience($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_SERVICE.'.empNumber','left');
		$this->db->where(TABLE_SERVICE.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_SERVICE);
		return $objQuery->result_array();	
	}

	function getAppointData($intAppointmentId = '')
	{		
		if($intAppointmentId != "")
		{
			$this->db->where($this->tableAppointid,$intAppointmentId);
		}
		$objQuery = $this->db->get($this->tableAppoint);
		return $objQuery->result_array();	
	}

	function getSepCauseData($strSepCause = '')
	{		
		if($strSepCause != "")
		{
			$this->db->where($this->tableSepCauseid,$strSepCause);
		}
		$objQuery = $this->db->get($this->tableSepCause);
		return $objQuery->result_array();	
	}

	// submission of requests
	function submit_request($arrData)
	{
		$this->db->insert('tblemprequest', $arrData);
		return $this->db->insert_id();		
	}
	// function submitProfile($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitFam($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitEduc($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }	
	// function submitTraining($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitExam($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitChild($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitTax($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitRef($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }
	// function submitVol($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }	
	// function submitWorkExp($arrData)
	// {
	// 	$this->db->insert('tblemprequest', $arrData);
	// 	return $this->db->insert_id();		
	// }

	// check existence
	function checkExist($strSname = '', $strFname = '')
	{		
		$strSQL = " SELECT * FROM tblemprequest					
					WHERE  
					requestDetails ='$strSname' OR
					requestDate ='$strFname'					
					";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

	// saving updates
	function save($arrData, $intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->update('tblemprequest', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	
	// deleting enties
	function delete($intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->delete('tblemprequest'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
}
