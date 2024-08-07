<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Requests extends MY_Controller
{

	var $arrData;

	function __construct()
	{
		parent::__construct();
		$this->load->model(array('libraries/Request_model', 'employee/Notification_model'));
	}

	public function cancel_request()
	{
		$arrpost = $this->input->post();
		if (!empty($arrpost)) :
			$requestid = $arrpost['txtreqid'];
			$arrData = array('requestStatus' => 'Cancelled', 'statusDate' => date('Y-m-d'));
			$this->Request_model->update_employeeRequest($arrData, $requestid);
			$this->session->set_flashdata('strSuccessMsg', 'Schedule added successfully.');
			redirect('employee/notification');
		endif;
	}

	public function update_to()
	{

		$arrdata = [];
		$arrpost = $this->input->post();

		$officer_empid = $this->session->userdata('sessEmpNo');
		$office = employee_office($officer_empid);
		$requestFlow = $this->Request_model->getRequestFlow($office);

		$requestdetails = $this->Request_model->getSelectedRequest($arrpost['txtto_id']);

		$arrRequest = $this->Notification_model->check_request_flow_and_signatories($requestFlow, $requestdetails);
		$req = array_shift($arrRequest);

		if ((strpos($req['req_nextsign'], $officer_empid) !== false)) :
			$req['req_desti'] = $this->Notification_model->getDestination($req['req_nextsign']);
			$req['req_empname'] = employee_name($req['req_emp']);
		endif;

		switch ($req['req_sign_no']) {
			case 'Signatory1':
				$arrdata['Signatory1'] = $officer_empid;
				$arrdata['Sig1DateTime'] = date('Y-m-d');
				break;
			case 'Signatory2':
				$arrdata['Signatory2'] = $officer_empid;
				$arrdata['Sig2DateTime'] = date('Y-m-d');
				break;
			case 'Signatory3':
				$arrdata['Signatory3'] = $officer_empid;
				$arrdata['Sig3DateTime'] = date('Y-m-d');
				break;
			case 'SignatoryFin':
				$arrdata['SignatoryFin'] = $officer_empid;
				$arrdata['SigFinDateTime'] = date('Y-m-d');
				break;
			default:
				break;
		}

		$arrdata['statusDate'] = date('Y-m-d h:i:s');
		$arrdata['requestStatus'] = strtoupper($arrpost['selto_stat']);
		$arrdata['remarks'] = $arrpost['txtto_remarks'];


		$requestid = $arrpost['txtto_id'];
		$this->Request_model->update_employeeRequest($arrdata, $requestid);
		$this->session->set_flashdata('strSuccessMsg', 'Schedule added successfully.');
		redirect('employee/notification');
	}

	public function update_ob(){
		$arrdata = [];
		$arrpost = $this->input->post();
		$arrget = $this->input->get();

		$officer_empid = $this->session->userdata('sessEmpNo');
		$office = employee_office($officer_empid);
		$requestFlow = $this->Request_model->getRequestFlow($office);
		
		$requestdetails = $this->Request_model->getSelectedRequest($arrpost['txtob_id']);
		
		$arrRequest = $this->Notification_model->check_request_flow_and_signatories($requestFlow, $requestdetails);
		$req = array_shift($arrRequest);
		
		if ((strpos($req['req_nextsign'], $officer_empid) !== false)) :
			$req['req_desti'] = $this->Notification_model->getDestination($req['req_nextsign']);
			$req['req_empname'] = employee_name($req['req_emp']);
		endif;

		switch ($req['req_sign_no']) { 
			case 'Signatory1':
				$arrdata['Signatory1'] = $officer_empid;
				$arrdata['Sig1DateTime'] = date('Y-m-d');
				break;
			case 'Signatory2':
				$arrdata['Signatory2'] = $officer_empid;
				$arrdata['Sig2DateTime'] = date('Y-m-d');
				break;
			case 'Signatory3':
				$arrdata['Signatory3'] = $officer_empid;
				$arrdata['Sig3DateTime'] = date('Y-m-d');
				break;
			case 'SignatoryFin':
				$arrdata['SignatoryFin'] = $officer_empid;
				$arrdata['SigFinDateTime'] = date('Y-m-d');
				break;
			default:
				break;
		}
		
		$arrdata['statusDate'] = date('Y-m-d h:i:s');
		$arrdata['requestStatus'] = strtoupper($arrpost['selob_stat']);
		$arrdata['remarks'] = $arrpost['txtob_remarks'];
	
		$requestid = $arrpost['txtob_id'];
		$this->Request_model->update_employeeRequest($arrdata, $requestid);

		$this->session->set_flashdata('strSuccessMsg', 'Request has been '.strtolower($arrpost['selob_stat']).'!');
		redirect('officer/tasks?month='.$arrget['month'].'&yr='.$arrget['year']);

	}
}
