<?php 
/** 
Purpose of file:    Controller for Reports
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

	var $arrData;

	function __construct() 
	{
        parent::__construct();
        $this->load->model(array('employee/reports_model'));
    }

	public function index()
	{
		// $this->arrData['arrAppointStatuses'] = $this->appointment_status_model->getData();
		$this->template->load('template/template_view', 'employee/reports/reports_view', $this->arrData);
	}
	
	public function submit()
    {
    	$arrPost = $this->input->post();
		if(!empty($arrPost))
		{
			$strReporttype=$arrPost['strReporttype'];
			$strRemittype=$arrPost['strRemittype'];
			$month=$arrPost['month'];
			$date=$arrPost['date'];
			$strStatus=$arrPost['strStatus'];
			$strCode=$arrPost['strCode'];
		
			if(!empty($strReporttype))
			{	
				if( count($this->reports_model->checkExist($strReporttype))==0 )
				{
					$arrData = array(
						'requestDetails'=>$strReporttype.';'.$strRemittype.';'.$month.';'.$date,
						'requestDate'=>date('Y-m-d'),
						'requestStatus'=>$strStatus,
						'requestCode'=>$strCode,
						'empNumber'=>$_SESSION['sessEmpNo']
						/*'requestDate'=>$dtmOBrequestdate,*/
						// 'requestStatus'=>
					);
					$blnReturn  = $this->reports_model->submit($arrData);

					if(count($blnReturn)>0)
					{	
						log_action($this->session->userdata('sessEmpNo'),'HR Module','tblemprequest','Added '.$strReporttype.' Reports',implode(';',$arrData),'');
						$this->session->set_flashdata('strSuccessMsg','Request has been submitted.');
					}
					redirect('employee/reports');
				}
				else
				{	
					$this->session->set_flashdata('strErrorMsg','Request already exists.');
					//$this->session->set_flashdata('strOBtype',$strOBtype);
					redirect('employee/reports');
				}
			}
		}
    	$this->template->load('template/template_view','employee/reports/reports_view',$this->arrData);
    }

    public function generate()
	{
		
		$this->load->library('fpdf_gen');
		$arrGet=$this->input->get();
		$rpt=$arrGet['rpt'];

		switch($rpt)
		{
			case 'reportOB': 
				$this->load->model(array('reports/ReportOB_rpt_model'));	
				$this->ReportOB_rpt_model->generate($arrGet['req_id']);
				echo $this->fpdf->Output();	
			break;
			case 'reportTO': 
				$this->load->model(array('reports/ReportTO_rpt_model'));
				// $arrData=array('strDestination'=>$arrGet['desti'],'dtmTOdatefrom'=>$arrGet['todatefrom'],'dtmTOdateto'=>$arrGet['todateto'],'strPurpose'=>$arrGet['purpose'],'strMeal'=>$arrGet['meal'],'strEmpNo'=>$arrGet['empno']);
				$arrData=array('strDestination'=>$arrGet['desti'],'dtmTOdatefrom'=>$arrGet['todatefrom'],'dtmTOdateto'=>$arrGet['todateto'],'strPurpose'=>$arrGet['purpose'],'strMeal'=>$arrGet['meal'],'strEmpNo'=>$_SESSION['sessEmpNo']);
				$this->ReportTO_rpt_model->generate($arrData);
				echo $this->fpdf->Output();	
			break;
			case 'reportLeave': 
				$this->load->model(array('reports/ReportLeave_rpt_model'));	
				$this->ReportLeave_rpt_model->generate($arrGet['req_id']);
				// echo $this->fpdf->Output();	
			break;
			case 'reportDTRupdate': 
				$this->load->model(array('reports/ReportDTRupdate_rpt_model'));				
				$arrData=array('dtmDTRupdate'=>$arrGet['dtrupdate'],'strOldMorningIn'=>$arrGet['oldmorin'],'strOldMorningOut'=>$arrGet['oldmorout'],'strOldAfternoonIn'=>$arrGet['oldafin'],'strOldAfternoonOut'=>$arrGet['oldaftout'],'strOldOvertimeIn'=>$arrGet['oldOTin'],'strOldOvertimeOut'=>$arrGet['oldOTout'],'dtmMorningIn'=>$arrGet['morningin'],'dtmMorningOut'=>$arrGet['morningout'],'dtmAfternoonIn'=>$arrGet['aftnoonin'],'dtmAfternoonOut'=>$arrGet['aftnoonout'],'dtmOvertimeIn'=>$arrGet['OTtimein'],'dtmOvertimeOut'=>$arrGet['OTtimeout'],'strReason'=>$arrGet['reason'],'dtmMonthOf'=>$arrGet['month'],'strEvidence'=>$arrGet['evidence'],'strSignatory'=>$arrGet['signatory']);
				$this->ReportDTRupdate_rpt_model->generate($arrData);
				
			break;
			case 'reportCL': 
				$this->load->model(array('reports/ReportCL_rpt_model'));
				$arrData=array('dtmComLeave'=>$arrGet['comleave'],'dtmMorningIn'=>$arrGet['morningin'],'dtmMorningOut'=>$arrGet['morningout'],'dtmAfternoonIn'=>$arrGet['aftrnoonin'],'dtmAfternoonOut'=>$arrGet['aftrnoonout'],'strPurpose'=>$arrGet['purpose'],'strRecommend'=>$arrGet['reco'],'strApproval'=>$arrGet['approval']);			
				$this->ReportCL_rpt_model->generate($arrData);
				echo $this->fpdf->Output();	
			break;
			case 'reportPDSupdate': 
				$this->load->model(array('reports/ReportPDSupdate_rpt_model'));
				if(isset($arrGet['empNumber']))
					$arrData=array('empNumber'=>$arrGet['empNumber']);
				else
					$arrData=array();			
				$this->ReportPDSupdate_rpt_model->generate($arrData);
				echo $this->fpdf->Output();	
			break;

		
		}
	}


}
