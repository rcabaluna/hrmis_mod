<?php

/** 
Purpose of file:    Model for Position Library
Author:             Edgardo P. Catorce Jr.
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
 **/
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Position_model extends CI_Model
{

	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}

	function getData($intPositionId = '')
	{
		if ($intPositionId != '') :
			return $this->db->order_by('positionDesc', 'ASC')->get_where('tblposition', array('positionId' => $intPositionId))->result_array();
		// $this->db->order_by('positionDesc', 'ASC');
		else :
			return $this->db->order_by('positionDesc', 'ASC')->get_where('tblposition')->result_array();
		// $this->db->order_by('positionDesc', 'ASC');
		endif;
	}

	function getDataByFields($fieldname, $value, $fields = "*")
	{
		$this->db->select($fields);
		return $this->db->get_where('tblempposition', array($fieldname => $value))->result_array();
	}

	function getEmployee_Position($appt)
	{
		return $this->db->get_where('tblempposition', array('appointmentCode' => $appt, 'statusOfAppointment' => 'In-Service'))->result_array();
	}

	function add($arrData)
	{
		$this->db->insert('tblposition', $arrData);
		return $this->db->insert_id();
	}

	function editPosition($arrData, $arrwhere)
	{
		$this->db->where($arrwhere);
		$this->db->update('tblempposition', $arrData);
		return $this->db->affected_rows();
	}

	function checkExist($strPositionCode = '', $strPositionDescription = '')
	{
		$strSQL = " SELECT * FROM tblposition
					WHERE positionCode ='$strPositionCode' OR positionDesc ='$strPositionDescription'";
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();
	}

	function save($arrData, $intPositionId)
	{
		$this->db->where('positionId', $intPositionId);
		$this->db->update('tblposition', $arrData);
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

	function delete($intPositionId)
	{
		$this->db->where('positionId', $intPositionId);
		$this->db->delete('tblposition');
		return $this->db->affected_rows() > 0 ? TRUE : FALSE;
	}

	function getemployees_bygroup($groupno, $officename)
	{
		return $this->db->get_where('tblempposition', array('group' . $groupno => $officename, 'statusOfAppointment' => 'In-Service'))->result_array();
	}

	function getemployees_byoffice($office){
		$this->db->select('*');
		$this->db->from('tblempposition');
		$this->db->group_start();
	
		for ($i = 1; $i <= 5; $i++) {
			if ($i === 1) {
				
				$this->db->where("group$i", $office);
			} else {
				$this->db->or_where("group$i", $office);
			}
		}
	
		$this->db->group_end(); 
		$query = $this->db->get();
	
		return $query->result();
	}
}
