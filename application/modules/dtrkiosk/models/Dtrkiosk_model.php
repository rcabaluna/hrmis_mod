<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dtrkiosk_model extends CI_Model {

	function __construct()
	{
		$this->load->database();
		$this->db->initialize();	
	}
	
	function get_present_employees()
	{
		$this->db->order_by('surname');
		$this->db->select('tblemppersonal.empNumber,surname,firstname,middlename,middleInitial,nameExtension,inAM,outAM,inPM,outPM');
		$this->db->join('tblemppersonal',' tblemppersonal.empNumber = tblempdtr.empNumber','left');
		$res = $this->db->get_where('tblempdtr', array('dtrDate' => date('Y-m-d')))->result_array();
		
		return $res;
	}

	function get_absent_employees()
	{
		$employees = array();
		$emp_dtr = $this->db->select('empNumber')->get_where('tblempdtr', array('dtrDate' => date('Y-m-d')))->result_array();

		$this->db->order_by('surname');
		$this->db->select('tblemppersonal.empNumber,surname,firstname,middlename,middleInitial,nameExtension');
		$this->db->join('tblemppersonal',' tblemppersonal.empNumber = tblempposition.empNumber','left');
		if(count($emp_dtr) > 0):
			$this->db->where_not_in('tblempposition.empNumber', array_column($emp_dtr,'empNumber'));
		endif;
		$employees = $this->db->get_where('tblempposition', array('statusOfAppointment' => 'In-Service', 'dtrSwitch' => 'Y'))->result_array();
		
		return $employees;
	}

	function get_ob_employees()
	{
		$this->db->group_by('tblempob.empNumber');
		$this->db->select('tblemppersonal.empNumber,surname,firstname,middlename,middleInitial,nameExtension');
		$this->db->join('tblemppersonal',' tblemppersonal.empNumber = tblempob.empNumber','left');
		$this->db->where("'".date('Y-m-d')."' >= obDateFrom");
		$this->db->where("'".date('Y-m-d')."' <= obDateTo");
		$employees = $this->db->get('tblempob')->result_array();
		
		return $employees;
	}

	function get_leave_employees()
	{
		$this->db->group_by('tblempleave.empNumber');
		$this->db->select('tblemppersonal.empNumber,surname,firstname,middlename,middleInitial,nameExtension');
		$this->db->join('tblemppersonal',' tblemppersonal.empNumber = tblempleave.empNumber','left');
		$this->db->where("'".date('Y-m-d')."' >= leaveFrom");
		$this->db->where("'".date('Y-m-d')."' <= leaveTo");
		$employees = $this->db->get('tblempleave')->result_array();
		
		return $employees;
	}
	
	function save_hcd($arrdata)
	{
		$result = $this->db->insert('tblonlinedtr_hcd', $arrdata);

		$status = ($result) ? 'success': 'error';
        $message = ($result)
            ? "Health Check Declaration has been successfully added."
            : 'Unable to add data.';

        return
            array(
                'status'  => $status,
                'message' => $message
            );
	}

	public function delete_dtr($empno,$dtrdate)
    {
        $this->db->where('empNumber', $empno);
        $this->db->where("dtrDate",$dtrdate);
        $result = $this->db->delete('tblempdtr');

        $status = ($result) ? 'success': 'error';
        $message = ($result)
            ? "DTR today has been successfully deleted."
            : 'Unable to delete data.';

        return
            array(
                'status'  => $status,
                'message' => $message
            );
    }
}
