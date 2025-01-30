<?php 
/** 
Purpose of file:    Model for PDS update
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Update_pds_model extends CI_Model {

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
	
	function getall_request($empno='')
	{
		if($empno!=''):
			$this->db->where('empNumber',$empno);
		endif;
		$this->db->like('requestCode', '201', 'after',false);
		return $this->db->order_by('requestDate','DESC')->get('tblemprequest')->result_array();
	}

	function getpds_request($reqid='')
	{
		if($reqid!=''):
			$res = $this->db->get_where('tblemprequest',array('requestID' => $reqid))->result_array();
			return count($res) > 0 ? $res[0] : array();
		else:
			return $this->db->get('tblemprequest')->result_array();
		endif;

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

	function geteduc_data($educid)
	{		
		$res = $this->db->get_where('tblempschool',array('SchoolIndex' => $educid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function gettra_data($traid)
	{		
		$res = $this->db->get_where('tblemptraining',array('TrainingIndex' => $traid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getexam_data($examid)
	{		
		$res = $this->db->get_where('tblempexam',array('ExamIndex' => $examid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getchild_data($childid)
	{		
		$res = $this->db->get_where('tblempchild',array('childCode' => $childid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getreference_data($refid)
	{		
		$res = $this->db->get_where('tblempreference',array('ReferenceIndex' => $refid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getvoluntary_data($volid)
	{		
		$res = $this->db->get_where('tblempvoluntarywork',array('VoluntaryIndex' => $volid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getworkxp_data($wxpid)
	{		
		$res = $this->db->get_where('tblservicerecord',array('serviceRecID' => $wxpid))->result_array();
		return count($res) > 0 ? $res[0] : array();
	}

	function getChildren($empnumber)
	{		
		return $this->db->get_where('tblempchild',array('empNumber' => $empnumber))->result_array();
	}

	function save_children($arrData)
	{
		$this->db->insert('tblempchild', $arrData);
		return $this->db->insert_id();
	}

	function update_children($arrData, $child_id)
	{
		$this->db->where('childCode', $child_id);
		$this->db->update('tblempchild', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
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

	function save_school($arrData)
	{
		$this->db->insert($this->tableSchool, $arrData);
		return $this->db->insert_id();
	}

	function update_school($arrData, $schoolid)
	{
		$this->db->where('SchoolIndex', $schoolid);
		$this->db->update($this->tableSchool, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
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
		// $this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_TRAINING.'.empNumber','left');
		$this->db->where($this->tableTraining.'.empNumber',$strEmpNumber);
		$this->db->order_by('tblemptraining.'.$this->tableTrainingid,'ASC');
		$objQuery = $this->db->get($this->tableTraining);
		return $objQuery->result_array();	
	}

	function save_training($arrData)
	{
		$this->db->insert($this->tableTraining, $arrData);
		echo $this->db->insert_id();
	}

	function update_training($arrData, $training_id)
	{
		$this->db->where('TrainingIndex', $training_id);
		$this->db->update($this->tableTraining, $arrData);
		echo $this->db->affected_rows()>0?TRUE:FALSE;
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

	function save_eligibility($arrData)
	{
		$this->db->insert('tblempexam', $arrData);
		echo $this->db->insert_id();
	}

	function update_eligibility($arrData, $exam_id)
	{
		$this->db->where('ExamIndex', $exam_id);
		$this->db->update('tblempexam', $arrData);
		echo $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function getRefData($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_REFERENCE.'.empNumber','left');
		$this->db->where(TABLE_REFERENCE.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_REFERENCE);
		return $objQuery->result_array();	
	}

	function save_reference($arrData)
	{
		$this->db->insert('tblempreference', $arrData);
		return $this->db->insert_id();
	}

	function update_reference($arrData, $ref_id)
	{
		$this->db->where('ReferenceIndex', $ref_id);
		$this->db->update('tblempreference', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function getVoluntary($strEmpNumber)
	{		
		$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.TABLE_VOLUNTARY.'.empNumber','left');
		$this->db->where(TABLE_VOLUNTARY.'.empNumber',$strEmpNumber);
		$objQuery = $this->db->get(TABLE_VOLUNTARY);
		return $objQuery->result_array();	
	}

	function save_voluntary($arrData)
	{
		$this->db->insert('tblempvoluntarywork', $arrData);
		return $this->db->insert_id();
	}

	function update_voluntary($arrData, $vol_id)
	{
		$this->db->where('VoluntaryIndex', $vol_id);
		$this->db->update('tblempvoluntarywork', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
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

	function save_workxp($arrData)
	{
		$this->db->insert('tblservicerecord', $arrData);
		return $this->db->insert_id();
	}

	function update_workxp($arrData, $xp_id)
	{
		$this->db->where('serviceRecID', $xp_id);
		$this->db->update('tblservicerecord', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
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

	# submission of requests
	function submit_request($arrData)
	{
		$this->db->insert('tblemprequest', $arrData);
		return $this->db->insert_id();		
	}

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

	function save_personal($arrData, $empno)
	{
		$this->db->where('empNumber', $empno);
		$this->db->update('tblemppersonal', $arrData);
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
