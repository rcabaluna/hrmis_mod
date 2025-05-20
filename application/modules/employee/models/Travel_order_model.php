<?php 
/** 
Purpose of file:    Model for Travel Order
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Travel_order_model extends CI_Model {

	var $table = 'tblemprequest';
	var $tableid = 'requestID';

	function __construct()
	{
		$this->load->database();
		$this->db->initialize();	
	}
	
	function getData($reqid='')
	{
		if($reqid!=''):
			$res = $this->db->get_where('tblemprequest',array('requestID' => $reqid))->result_array();
			return count($res) > 0 ? $res[0] : array();
		else:
			return $this->db->get('tblemprequest')->result_array();
		endif;
	}

	function getall_request($empno='')
	{
		if($empno!=''):
			$this->db->where('empNumber',$empno);
		endif;
		return $this->db->order_by('requestDate','DESC')->get_where('tblemprequest',array('requestCode' => 'TO'))->result_array();
	}

	function submit($arrData)
	{
		$this->db->insert('tblemprequest', $arrData);
		return $this->db->insert_id();		
	}

	function submit_funding_details($arrData)
	{
		$this->db->insert('tblempto_req_details', $arrData);
		return $this->db->insert_id();		
	}

	function add($arrData)
	{
		$this->db->insert('tblemptravelorder', $arrData);
		return $this->db->insert_id();		
	}
	
	function checkExist($str_details)
	{		
		$strSQL = " SELECT * FROM tblemprequest					
					WHERE  
					requestDetails = ?";
		//echo $strSQL;exit(1);
		$objQuery = $this->db->query($strSQL, array($str_details));
		return $objQuery->result_array();	
	}

	function save($arrData, $intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->update('tblemprequest', $arrData);
		//echo $this->db->affected_rows();
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
	function delete($intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->delete('tblemprequest'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function getEmployeeTO($empid,$datefrom,$dateto)
	{
		$this->db->where('empNumber', $empid);
		$this->db->where("(toDateFrom between '".$datefrom."' and '".$dateto."' or toDateTo between '".$datefrom."' and '".$dateto."')");
		return $this->db->get('tblemptravelorder')->result_array();
	}
		
}
