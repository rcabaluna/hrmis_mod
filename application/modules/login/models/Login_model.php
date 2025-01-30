<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		//$this->db->initialize();	
	}
	
	public function authenticate($strUsername,$strPassword)
	{

		$this->db->select('tblempaccount.*,tblempposition.*,tblemppersonal.*');
		$this->db->join('tblempposition','tblempposition.empNumber=tblempaccount.empNumber','left');
		$this->db->join('tblemppersonal','tblemppersonal.empNumber=tblempaccount.empNumber','left');
		$rs = $this->db->get_where('tblempaccount', array('userName' => $strUsername, 'statusOfAppointment'=>'In-Service'))->result_array();

		if(count($rs) > 0):
			$strPass = $this->db->escape_str($rs[0]['userPassword']);
			$blnValid = password_verify($strPassword,$rs[0]['userPassword']);
			
			if(md5($strPassword) == $rs[0]['userPassword'] || $blnValid):
				return $rs;
			endif;
		endif;

		return array();
	}		
}
/* End of file login_model.php */
/* Location: ./application/modules/login/models/login_model.php */