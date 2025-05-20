<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Remittance_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
	}
	
	function getRemittance($empid='', $code, $from, $to, $appt='')
	{
		$arremp = $this->db->select('empNumber')->get_where('tblempposition',array('appointmentCode' => $appt,'statusOfAppointment' =>'In-Service'))->result_array();

		$this->db->where("deductYear between '".str_replace(' ', '', $from)."' and '".str_replace(' ', '', $to)."'");
		$this->db->where('tblempdeductionremit.deductionCode',$code);
		if($empid != ''){
			$this->db->where('empNumber',$empid);
		}
		if(count($arremp) > 0){
			$this->db->where_in('empNumber',array_column($arremp,'empNumber'));	
		}
		$res = $this->db->join('tbldeduction', 'tbldeduction.deductionCode = tblempdeductionremit.deductionCode', 'left')->get_where('tblempdeductionremit')->result_array();
		
		return $res;
	}
		
}
/* End of file Remittance_model.php */
/* Location: ./application/modules/finance/models/Remittance_model.php */