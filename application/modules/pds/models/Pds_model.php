<?php 
/** 
Purpose of file:    Model for PDS 
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pds_model extends CI_Model {

	var $table = 'tblemppersonal';
	var $tableid = 'empNumber';

	var $tblchild = 'tblempchild';
	var $tblchildid = 'childCode';

	var $tbleduc = 'tblempschool';
	var $tbleducid = 'SchoolIndex';

	var $tblexam = 'tblempexam';
	var $tblexamid = 'ExamIndex';

	var $tblservice = 'tblservicerecord';
	var $tblserviceid = 'serviceRecID';

	var $tblvol = 'tblempvoluntarywork';
	var $tblvolid = 'VoluntaryIndex';

	var $tbltraining = 'tblemptraining';
	var $tbltrainingid = 'TrainingIndex';

	var $tblposition = 'tblempposition';
	var $tblpositionid = 'empNumber';

	var $table9 = 'tblempduties';
	var $tableid9 = 'duties';

	var $table10 = 'tblplantilladuties';
	var $tableid10 = 'itemDuties';

	var $tableCharRef = 'tblempreference';
	var $tableCharRefId = 'ReferenceIndex';


	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
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

	function getChild($intChildCode = '')
	{		
		if($intChildCode != "")
		{
			$this->db->where($this->tblchildid,$intChildCode);
		}
		$objQuery = $this->db->get($this->tblchild);
		return $objQuery->result_array();	
	}

	function getEduc($intSchoolIndex = '')
	{		
		if($intSchoolIndex != "")
		{
			$this->db->where($this->tbleducid,$intSchoolIndex);
		}
		$objQuery = $this->db->get($this->tbleduc);
		return $objQuery->result_array();	
	}

	function getExam($intExamIndex = '')
	{		
		if($intExamIndex != "")
		{
			$this->db->where($this->tblexamid,$intExamIndex);
		}
		$objQuery = $this->db->get($this->tblexam);
		return $objQuery->result_array();	
	}

	function getTraining($strtraIndex = '')
	{		
		if($strtraIndex != "")
		{
			$this->db->where($this->tbltrainingid,$strtraIndex);
		}
		$objQuery = $this->db->get($this->tbltraining);
		return $objQuery->result_array();	
	}


	function getWorkExp($intServiceId = '')
	{		
		if($intServiceId != "")
		{
			$this->db->where($this->tblserviceid,$intServiceId);
		}
		$objQuery = $this->db->get($this->tblservice);
		return $objQuery->result_array();	
	}

	function getPosition($strEmpNumber = '')
	{		
		if($strEmpNumber != "")
		{
			$this->db->where($this->tblpositionid,$strEmpNumber);
		}
		$objQuery = $this->db->get($this->tblposition);
		return $objQuery->result_array();	
	}

	function getImage($ImageId = '')
	{		
		if($strEmpNumber != "")
		{
			$this->db->where($this->tableid,$strEmpNumber);
		}
		// $this->db->join('tblagencyimages','tblagencyimages.id = '.$this->table.'.agencyName','left');
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	function add($arrData)
	{
		$this->db->insert($this->table, $arrData);
		return $this->db->insert_id();		
	}

	function add_child($arrData)
	{
		$this->db->insert($this->tblchild, $arrData);
		return $this->db->insert_id();		
	}

	function delete_child($code)
	{
		$this->db->where($this->tblchildid, $code);
		$this->db->delete($this->tblchild); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function checkExist($strAgencyName = '', $strAgencyCode = '')
	{		
		
		$this->db->where('agencyName',$strAgencyName);
		$this->db->or_where('agencyCode', $strAgencyCode);			
		
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}
	
	function save_personal($arrData, $strEmpNumber)
	{
		$this->db->where($this->tableid, $strEmpNumber);
		$this->db->update($this->table, $arrData);
		//echo $this->db->last_query();exit(1);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_child($arrData, $intChildCode)
	{
		$this->db->where($this->tblchildid, $intChildCode);
		$this->db->update($this->tblchild, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	# BEGIN EDUCATION
	function add_educ($arrData)
	{
		$this->db->insert($this->tbleduc, $arrData);
		return $this->db->insert_id();
	}

	function save_educ($arrData, $intSchoolIndex)
	{
		$this->db->where($this->tbleducid, $intSchoolIndex);
		$this->db->update($this->tbleduc, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_educ($intSchoolIndex)
	{
		$this->db->where($this->tbleducid, $intSchoolIndex);
		$this->db->delete($this->tbleduc); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END EDUCATION

	# BEGIN EXAM
	function add_exam($arrData)
	{
		$this->db->insert($this->tblexam, $arrData);
		return $this->db->insert_id();
	}

	function save_exam($arrData, $intExamIndex)
	{
		$this->db->where($this->tblexamid, $intExamIndex);
		$this->db->update($this->tblexam, $arrData);
		// echo $this->db->last_query();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_exam($examid)
	{
		$this->db->where($this->tblexamid, $examid);
		$this->db->delete($this->tblexam); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END EXAM

	# BEGIN WORK EXPERIENCE
	function add_workExp($arrData)
	{
		$this->db->insert($this->tblservice, $arrData);
		return $this->db->insert_id();
	}

	function save_workExp($arrData, $intServiceId)
	{
		$this->db->where($this->tblserviceid, $intServiceId);
		$this->db->update($this->tblservice, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_workExp($intServiceId)
	{
		$this->db->where($this->tblserviceid, $intServiceId);
		$this->db->delete($this->tblservice); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END WORK EXPERIENCE

	# BEGIN VOLUNTARY WORK
	function add_volWorks($arrData)
	{
		$this->db->insert($this->tblvol, $arrData);
		return $this->db->insert_id();
	}

	function save_volWorks($arrData, $strVolIndex)
	{
		$this->db->where($this->tblvolid, $strVolIndex);
		$this->db->update($this->tblvol, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_volWorks($strVolIndex)
	{
		$this->db->where($this->tblvolid, $strVolIndex);
		$this->db->delete($this->tblvol); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END VOLUNTARY WORK

	# ADD TRAINING
	function add_training($arrData)
	{
		$this->db->insert($this->tbltraining, $arrData);
		return $this->db->insert_id();
	}

	function save_training($arrData, $strtraIndex)
	{
		$this->db->where($this->tbltrainingid, $strtraIndex);
		$this->db->update($this->tbltraining, $arrData);
			// echo $this->db->last_query();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_training($strtraIndex)
	{
		$this->db->where($this->tbltrainingid, $strtraIndex);
		$this->db->delete($this->tbltraining); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END TRAINING

	# BEGIN CHARACTER REFERENCES
	function add_char_refs($arrData)
	{
		$this->db->insert($this->tableCharRef, $arrData);
		return $this->db->insert_id();
	}

	function save_char_refs($arrData, $strcharIndex)
	{
		$this->db->where($this->tableCharRefId, $strcharIndex);
		$this->db->update($this->tableCharRef, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_char_refs($strcharIndex)
	{
		$this->db->where($this->tableCharRefId, $strcharIndex);
		$this->db->delete($this->tableCharRef); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END CHARACTER REFERENCES

	# BEGIN DUTIES & RESPONSIBILITIES
	#	- For Position
	function add_duties_position($arrData)
	{
		$this->db->insert('tblduties', $arrData);
		return $this->db->insert_id();
	}

	function save_duties_position($arrData, $duties_id)
	{
		$this->db->where('duties_index', $duties_id);
		$this->db->update('tblduties', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_duties_position($duties_id)
	{
		$this->db->where('duties_index', $duties_id);
		$this->db->delete('tblduties'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	#	- For Plantilla
	function add_duties_plantilla($arrData)
	{
		$this->db->insert('tblplantilladuties', $arrData);
		return $this->db->insert_id();
	}

	function save_duties_plantilla($arrData, $duties_id)
	{
		$this->db->where('plantilla_duties_index', $duties_id);
		$this->db->update('tblplantilladuties', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_duties_plantilla($duties_id)
	{
		$this->db->where('plantilla_duties_index', $duties_id);
		$this->db->delete('tblplantilladuties'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	#	- For Actual
	function add_duties_actual($arrData)
	{
		$this->db->insert('tblempduties', $arrData);
		return $this->db->insert_id();
	}

	function save_duties_actual($arrData, $duties_id)
	{
		$this->db->where('empduties_index', $duties_id);
		$this->db->update('tblempduties', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_duties_actual($duties_id)
	{
		$this->db->where('empduties_index', $duties_id);
		$this->db->delete('tblempduties'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END DUTIES & RESPONSIBILITIES

	# BEGIN APPOINTMENT ISSUE
	function add_apptIssue($arrData)
	{
		$this->db->insert('tblempappointment', $arrData);
		return $this->db->insert_id();
	}

	function save_apptIssue($arrData, $apptcode)
	{
		$this->db->where('appointmentissuedcode', $apptcode);
		$this->db->update('tblempappointment', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_apptIssue($apptcode)
	{
		$this->db->where('appointmentissuedcode', $apptcode);
		$this->db->delete('tblempappointment'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	# END APPOINTMENT ISSUE

	# BEGIN UPDATE EMPNUMBER
	function save_empnumber($old_empid,$new_empid)
	{
		$all_tables = $this->db->list_tables();

		foreach($all_tables as $tblname):
			if($this->db->field_exists('empNumber', $tblname)):
				$this->db->where('empNumber', $old_empid);
				$this->db->update($tblname, array('empNumber' => $new_empid));
			endif;
		endforeach;
	}
	# END UPDATE EMPNUMBER

	function save_skill($arrData, $strEmpNumber)
	{
		$this->db->where($this->tableid, $strEmpNumber);
		$this->db->update($this->table, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_position($arrData, $strEmpNumber)
	{
		$this->db->where($this->tblpositionid, $strEmpNumber);
		$this->db->update($this->tblposition, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_Duties($arrData, $strEmpNumber)
	{
		$this->db->where($this->tableid9, $strEmpNumber);
		$this->db->update($this->table9, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save($arrData, $strAgencyName)
	{
		$this->db->where($this->tableid, $strAgencyName);
		$this->db->update($this->table, $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function saveImage($arrData, $ImageId)
	{
		$this->db->where('id', $ImageId);
		$this->db->update('tblagencyimages', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
	function delete($strAgencyName)
	{
		$this->db->where($this->tableid, $strAgencyName);
		$this->db->delete($this->table); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function getDataByField($arrWhere,$appt)
	{
		if($appt == 'P'):
			$compInstance = $this->db->get_where('tblcomputationinstance',$arrWhere)->result_array();
			if(count($compInstance) > 0):
				$compInstance = $compInstance[0];
				$compDetails = $this->db->join('tblemppersonal', 'tblemppersonal.empNumber = tblcomputationdetails.empNumber', 'left')
									->order_by('tblemppersonal.surname', 'ASC')
									->order_by('tblemppersonal.firstname', 'ASC')
									->where('fk_id',$compInstance['id'])
									->where('(periodmonth='.$compInstance['month'].' AND periodyear='.$compInstance['year'].')')
									->get('tblcomputationdetails')->result_array();
				return $compDetails;
			endif;
		else:
		endif;
	}

	// EDIT EMPNUMBER
	function checkEmpNumExist($strEmpNumber = '')
	{		
		$this->db->where('empNumber',$strEmpNumber);	
		
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	// function getpayrollprocess($appt='')
 // 	{
 // 		// $arrProcesswith = $this->db->select('appointmentCode')->get_where('tblpayrollprocess',array('appointmentCode' => $appt,'processWith' =>'CT,PC1,PC2,PC3,PC4,PC5,CTI,E,O,PA,SE,SU,T,P'))->result_array();
 		
	// 	$sql ="SELECT `processWith` FROM `tblpayrollprocess` WHERE `appointmentCode`="P"";
	// 	echo $this->db->last_query();
 		
	// 	$objQuery = $this->db->query($sql);
	// 	return $objQuery->result_array();	

 // 	}

}
