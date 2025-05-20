<?php 
/** 
Purpose of file:    Model for Official Business
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Official_business_model extends CI_Model {

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
		return $this->db->order_by('requestID','DESC')->get_where('tblemprequest',array('requestCode' => 'OB'))->result_array();
	}

	function submit($arrData)
	{
		$this->db->insert('tblemprequest', $arrData);
		return $this->db->insert_id();		
	}

	function add($arrData)
	{
		$this->db->insert('tblempob', $arrData);
		return $this->db->insert_id();		
	}
	
	function checkExist($strOBtype = '', $dtmOBrequestdate = '')
	{		
		$strSQL = " SELECT * FROM tblemprequest					
					WHERE  
					requestDetails ='$strOBtype' AND
					requestDate ='$dtmOBrequestdate'					
					";
		$objQuery = $this->db->query($strSQL);
		return $objQuery->result_array();	
	}

	function save($arrData, $intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->update('tblemprequest', $arrData);
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}
		
	function delete($intReqId)
	{
		$this->db->where('requestID', $intReqId);
		$this->db->delete('tblemprequest'); 	
		return $this->db->affected_rows()>0?TRUE:FALSE;
	}

	function getEmployeeOB($empid,$datefrom,$dateto)
	{
		$this->db->where('empNumber', $empid);
		$this->db->where('approveHR', 'Y');
		$this->db->where("(obDateFrom between '".$datefrom."' and '".$dateto."' or obDateTo between '".$datefrom."' and '".$dateto."')");
		$res = $this->db->get('tblempob')->result_array();
		return $res;
	}

}
