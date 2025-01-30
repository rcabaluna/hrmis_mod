<?php 
/** 
Purpose of file:    Model for Educational level Library
Author:             Rose Anne Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Educ_level_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}
	
	function getData($intEducLevelId = '')
	{
		$this->db->order_by('levelCode');
		if($intEducLevelId != ""):
			return $this->db->get_where('tbleducationallevel', array('levelId' => $intEducLevelId))->result_array();
		else:
			return $this->db->get('tbleducationallevel')->result_array();
		endif;	
	}

	function add($arrData)
	{
		$this->db->insert('tbleducationallevel', $arrData);
		return $this->db->insert_id();		
	}
	
	function checkExist($strEducLevelCode = '', $strEducLevelDesc = '')
	{		
		$strSQL = " SELECT * FROM tbleducationallevel					
					WHERE  
					levelCode ='$strEducLevelCode' OR
					levelDesc ='$strEducLevelDesc'					
					";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

				
		
	function save($arrData, $intEducLevelId)
	{
		$this->db->where('levelId', $intEducLevelId);
		$this->db->update('tbleducationallevel', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
	function delete($intEducLevelId)
	{
		$this->db->where('levelId', $intEducLevelId);
		$this->db->delete('tbleducationallevel'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
}
