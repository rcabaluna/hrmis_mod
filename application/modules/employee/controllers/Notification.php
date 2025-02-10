<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends MY_Controller
{

	var $arrData;

	function __construct()
	{
		parent::__construct();
		$this->load->model(array('libraries/Request_model', 'Notification_model'));
	

	}

	public function index()
	{
		$arrRequest = array();
		$forhr_requests = array();

		# Employee Notification
		if (in_array($this->session->userdata('sessUserLevel'), array(5, 4, 3))) :
			$strEmpNo = $_SESSION['sessEmpNo'];
			$arrempRequest = array();
			//CHANGE PARAMETER TO "HR"
			// $requestFlow = $this->Request_model->getRequestFlow('ALLEMP');
			$requestFlow = $this->Request_model->getRequestFlow();

			$arremp_request = $this->Request_model->getEmployeeRequest2($strEmpNo);  
			$updated_requests = [];

			foreach ($arremp_request as $arremp_requestRow) {
				$destination = $this->Request_model->get_next_signatory_notif($arremp_requestRow, '', $arremp_requestRow['requestflowid']);
				$arremp_requestRow['destination'] = $destination; // Add destination to the row
				$updated_requests[] = $arremp_requestRow; // Push updated row into new array
			}

			$arrRequest = $updated_requests;

		endif;

		if (check_module() == 'officer') :
			$strEmpNo = $_SESSION['sessEmpNo'];
			$arrempRequest = array();

			$requestFlow = $this->Request_model->getRequestFlow(employee_office($this->session->userdata('sessEmpNo')));
			// $requestFlow = $this->Request_model->getRequestFlow('ALLEMP');
			$requestFlow = $this->Request_model->getRequestFlow($strEmpNo);
			$arremp_request = $this->Request_model->getEmployeeRequest2($strEmpNo);  
			$updated_requests = [];

			foreach ($arremp_request as $arremp_requestRow) {
				$destination = $this->Request_model->get_next_signatory_notif($arremp_requestRow, '', $arremp_requestRow['requestflowid']);
				$arremp_requestRow['destination'] = $destination; // Add destination to the row
				$updated_requests[] = $arremp_requestRow; // Push updated row into new array
			}

			$arrRequest = $updated_requests;
		endif;

		$this->arrData['arrRequest'] = $arrRequest;
		// $this->arrData['arrRequest'] = $forhr_requests;
		// $this->arrData['arrhr_request'] = count($this->Notification_model->check_request_flow_and_signatories($requestFlow,$emp_requests,1));

	
		$this->template->load('template/template_view', 'employee/notification/notification_view', $this->arrData);
	}
}
