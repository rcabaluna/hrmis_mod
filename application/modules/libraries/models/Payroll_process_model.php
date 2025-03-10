<?php 
/** 
Purpose of file:    Model for Payroll Process Library
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Payroll_process_model extends CI_Model {

	var $table = 'tblpayrollprocess';
	var $tableid = 'appointmentCode';

	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}

	function getData($intId = '')
	{		
		if($intId != "")
		{
			$this->db->where($this->table.'.'.$this->tableid,$intId);
		}
		$this->db->join('tblappointment','tblappointment.appointmentCode = '.$this->table.'.'.$this->tableid,'inner');
		$objQuery = $this->db->get($this->table);
		return $objQuery->result_array();	
	}

	function add($arrData)
	{
		$this->db->insert($this->table, $arrData);
		return $this->db->insert_id();		
	}

	function save($arrData, $intId)
	{
		$this->db->where($this->tableid, $intId);
		$this->db->update($this->table, $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
	function delete($intId)
	{
		$this->db->where($this->tableid, $intId);
		$this->db->delete($this->table); 	
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
}
