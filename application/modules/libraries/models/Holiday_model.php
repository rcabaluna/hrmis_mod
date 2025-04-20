<?php 
/** 
Purpose of file:    Model for Holiday Library
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Holiday_model extends CI_Model {

	var $table = 'tblholiday';
	var $tableid = 'holidayCode';

	var $table2 = 'tbllocalholiday';
	var $tableid2 = 'holidayCode';

	var $table3 = 'tblholidayyear';
	var $tableid3 = 'holidayId';

	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}
	
	function getData($strCode = '')
	{		
		if($strCode != "")
		{
			$this->db->where($this->tableid,$strCode);
		}
		// $this->db->join('tblholidayyear','tblholidayyear.holidayCode = '.$this->table.'.holidayCode','inner');
		// $this->db->order_by('holidayName');
		// $this->db->group_by($this->table.'.holidayCode');
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	function getHolidayName($intHolidayId = '')
	{	
		$this->db->Select('tblholiday.*,holidayName');
		if($intHolidayId != "")
		{
			$this->db->where('tblholiday.holidayId',$intHolidayId);
		}
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	function getManageHoliday($intHolidayId = '')
	{	
		$this->db->Select('tblholidayyear.*,tblholiday.holidayName');
		if($intHolidayId != "")
		{
			$this->db->where('tblholidayyear.holidayId',$intHolidayId);
		}
		$this->db->join('tblholidayyear','tblholidayyear.holidayCode = '.$this->table.'.holidayCode','');
		$this->db->order_by('holidayName');
		// $this->db->group_by($this->table.'.holidayCode');
		$objQuery = $this->db->get($this->table);
		//echo $this->db->last_query();
		return $objQuery->result_array();	
	}

	function getLocalHoliday($strCode = '')
	{		
		if($strCode != "")
		{
			$this->db->where($this->tableid2,$strCode);
		}
		$this->db->order_by('holidayDate desc');

		$objQuery = $this->db->get($this->table2);
		return $objQuery->result_array();	
	}

	function getLastLocalCode()
	{		
		$this->db->Select('holidayCode');
		$this->db->order_by('holidayCode desc');
		$objQuery = $this->db->get('tbllocalholiday');
		return $objQuery->result_array();	
	}

	function getHolidayDate($strCode = '')
	{		
		if($strCode != "")
		{
			$this->db->where($this->tableid3,$strCode);
		}
		//$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.$this->table.'.empNumber','left');

		$objQuery = $this->db->get($this->table3);
		return $objQuery->result_array();	
	}

	
	function getWorkSuspension($intHolidayId = '')
	{		
		$this->db->where('holidayCode','WS');
		if($intHolidayId != "")
		{
			$this->db->where('holidayId',$intHolidayId);
		}
		//$this->db->join('tblemppersonal','tblemppersonal.empNumber = '.$this->table.'.empNumber','left');
		$this->db->order_by('holidayCode');
		// $this->db->group_by('tblholidayyear'.'.holidayCode');
		$objQuery = $this->db->get('tblholidayyear');
		return $objQuery->result_array();	
	}


	//ADD
	function add($arrData)
	{
		$this->db->insert('tblholiday', $arrData);
		return $this->db->insert_id();		
	}

	function manage_add($arrData)
	{
		$this->db->insert('tblholidayyear', $arrData);
		return $this->db->insert_id();		
	}

	function add_local($arrData)
	{
		$this->db->insert('tbllocalholiday', $arrData);
		return $this->db->insert_id();		
	}

	function add_worksuspension($arrData)
	{
		$this->db->insert('tblholidayyear', $arrData);
		return $this->db->insert_id();		
	}

	
	//CHECK IF EXIST	
	function checkExist($strHolidayCode = '', $strHolidayName = '')
	{		
		$strSQL = " SELECT * FROM tblholiday					
					WHERE  
					holidayCode ='$strHolidayCode' OR
					holidayName ='$strHolidayName'					
					";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

	function checkHolidayExist($strHolidayName = '', $dtmHolidayDate = '')
	{		
		$strSQL = " SELECT * FROM tblholidayyear					
					WHERE  
					holidayCode ='$strHolidayName' AND
					holidayDate ='$dtmHolidayDate' 								
					";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

	function checkLocExist($strLocalName = '', $dtmHolidayDate = '')
	{		
		if($dtmHolidayDate!=''):
			$strSQL = " SELECT * FROM tbllocalholiday					
					WHERE  
					holidayCode ='$strLocalName' AND
					holidayName ='$dtmHolidayDate'					
					";
		else:
			$strSQL = "SELECT * FROM tbllocalholiday";
		endif;
		
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

	function checkWorkSuspensionExist($dtmSuspensionDate = '')
	{		
		$strSQL = " SELECT * FROM tblholidayyear					
					WHERE  
					holidayDate ='$dtmSuspensionDate'								
					";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}


	//SAVE
	function save($arrData, $strCode)
	{
		$this->db->where('holidayCode', $strCode);
		$this->db->update('tblholiday', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_manage($arrData, $intHolidayId)
	{
		$this->db->where('holidayId', $intHolidayId);
		$this->db->update('tblholidayyear', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_local($arrData, $strCode)
	{
		$this->db->where('holidayCode', $strCode);
		$this->db->update('tbllocalholiday', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function save_worksuspension($arrData, $intHolidayId)
	{
		$this->db->where('holidayId', $intHolidayId);
		$this->db->update('tblholidayyear', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	
	//DELETE	
	function delete($strCode)
	{
		$this->db->where('holidayCode', $strCode);
		$this->db->delete('tblholiday'); 	
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_manage($intHolidayId)
	{
		$this->db->where('holidayId', $intHolidayId);
		$this->db->delete('tblholidayyear'); 	
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function delete_local($strCode)
	{
		$this->db->where('holidayCode', $strCode);
		$this->db->delete('tbllocalholiday'); 	
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
	
	function delete_worksuspension($strCode)
	{
		$this->db->where('holidayId', $strCode);
		$this->db->delete('tblholidayyear'); 	
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function get_work_suspension($sdate,$edate)
	{
		$arrwork_suspension = array();
		$this->db->join('tblholiday','tblholiday.holidayCode = tblholidayyear.holidayCode','left');
		$this->db->where("(holidayDate >= '".$sdate."' and holidayDate <= '".$edate."')");
		$reg_holidays = $this->db->get_where('tblholidayyear')->result_array();
		
		foreach($reg_holidays as $rholiday):
			if($rholiday['holidayCode'] == 'WS' && $rholiday['holidayTime'] != NULL && $rholiday['holidayTime'] != ''):
				array_push($arrwork_suspension,$rholiday);
			endif;
		endforeach;

		return $arrwork_suspension;
	}

	function getAllHolidates($empid,$sdate,$edate)
	{
		$arrholiday_year = array();
		$this->db->where("(holidayDate >= '".$sdate."' and holidayDate <= '".$edate."')");
		$reg_holidays = $this->db->get_where('tblholidayyear')->result_array();
		
		foreach($reg_holidays as $rholiday):
			if($rholiday['holidayCode'] == 'WS'):
				if($rholiday['holidayTime'] == NULL || $rholiday['holidayTime'] == ''):
					array_push($arrholiday_year,$rholiday);
				endif;
			else:
				array_push($arrholiday_year,$rholiday);
			endif;
		endforeach;

		$this->db->select("concat(holidayYear,'-',LPAD(holidayMonth,2,0),'-',LPAD(holidayDay,2,0)) as holidate");
		$this->db->join('tblemplocalholiday','tblemplocalholiday.holidayCode = tbllocalholiday.holidayCode','left');
		$this->db->where("(STR_TO_DATE(concat(holidayYear,'-',holidayMonth,'-',holidayDay),'%Y-%m-%d') >= '".$sdate."' and STR_TO_DATE(concat(holidayYear,'-',holidayMonth,'-',holidayDay),'%Y-%m-%d') <= '".$edate."')");
		$localholidays = $this->db->get('tbllocalholiday')->result_array();

		$allholidays = array_merge(array_column($arrholiday_year,'holidayDate'),array_column($localholidays,'holidate'));
		
		return $allholidays;
	}

	function getHolidayDetails($date)
	{
		# Regular Holiday
		$reg_holidays = $this->db->join('tblholiday','tblholiday.holidayCode = tblholidayyear.holidayCode','left')->get_where('tblholidayyear',array('tblholidayyear.holidayDate' => $date))->result_array();
		
		# Local Holiday 
		$local_holidays = $this->db->get_where('tbllocalholiday',array('holidayDate' => $date))->result_array();
		
		return array_merge(array_column($reg_holidays,'holidayName'),array_column($local_holidays,'holidayName'));
	}

		
}
