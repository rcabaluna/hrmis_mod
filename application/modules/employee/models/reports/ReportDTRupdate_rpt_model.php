<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ReportDTRupdate_rpt_model extends CI_Model {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		$this->load->helper('report_helper');	
		//ini_set('display_errors','On');
		//$this->load->model(array());
	}

	public function getEmp($intEmpNumber = '')
	{		
		if($intEmpNumber != "")
		{
			$this->db->where('empNumber',$intEmpNumber);
		}
		$objQuery = $this->db->get('tblemppersonal');
		return $objQuery->result_array();		
	}

	function intToMonthFull($t_intMonth='')
	{
		$t_intMonth = $t_intMonth=='undefined' ? '' : $t_intMonth;
		if($t_intMonth!=''):
			$arrMonths = array(1=>"January", 2=>"February", 3=>"March", 
							4=>"April", 5=>"May", 6=>"June", 
							7=>"July", 8=>"August", 9=>"September", 
							10=>"October", 11=>"November", 12=>"December");
			return $arrMonths[$t_intMonth];
		else:
			return '';
		endif;
	}

	public function Header()
	{

	}
	
	function Footer()
	{		
		$this->fpdf->SetFont('Arial','',7);	
		$this->fpdf->Cell(50,3,date('Y-m-d h:i A'),0,0,'L');
		$this->fpdf->Cell(0,3,"Page ".$this->fpdf->PageNo(),0,0,'R');					
	}
	
	function generate($arrData)
	{
		$today =  date("F j, Y",strtotime(date("Y-m-d")));
		$dtmDTRupdate = $arrData['dtmDTRupdate']==''?'':date("F j, Y",strtotime($arrData['dtmDTRupdate']));
		$month = $arrData['dtmDTRupdate']==''?'':date("F",strtotime($arrData['dtmDTRupdate']));
		
		// old
		$strOldMorningIn= ($arrData['strOldMorningIn'] != "00:00:00") ?  date("h:i A", strtotime($arrData['strOldMorningIn'])) : '';
		$strOldMorningOut= ($arrData['strOldMorningOut'] != "00:00:00") ?  date("h:i A", strtotime($arrData['strOldMorningOut'])) : '';
		$strOldAfternoonIn= ($arrData['strOldAfternoonIn'] != "00:00:00") ?  date("h:i A", strtotime($arrData['strOldAfternoonIn'])) : '';
		$strOldAfternoonOut= ($arrData['strOldAfternoonOut'] != "00:00:00") ?  date("h:i A", strtotime($arrData['strOldAfternoonOut'])) : '';
		$strOldOvertimeIn= ($arrData['strOldOvertimeIn'] != "00:00:00") ?   date("h:i A", strtotime($arrData['strOldOvertimeIn'])) : '';
		$strOldOvertimeOut= ($arrData['strOldOvertimeOut'] != "00:00:00") ?  date("h:i A", strtotime($arrData['strOldOvertimeOut'])) : '';
		// new
		$dtmMorningIn = ($arrData['dtmMorningIn'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmMorningIn'])) : '';
		$dtmMorningOut = ($arrData['dtmMorningOut'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmMorningOut'])) : '';
		$dtmAfternoonIn = ($arrData['dtmAfternoonIn'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmAfternoonIn'])) : '';
		$dtmAfternoonOut = ($arrData['dtmAfternoonOut'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmAfternoonOut'])) : '';
		$dtmOvertimeIn = ($arrData['dtmOvertimeIn'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmOvertimeIn'])) : '';
		$dtmOvertimeOut = ($arrData['dtmOvertimeOut'] != "00:00:00") ? date("h:i A", strtotime($arrData['dtmOvertimeOut'])) : '';

		$strReason = $arrData['strReason'];
		// $dtmMonthOf = $arrData['dtmMonthOf'];
		
		$strEvidence = $arrData['strEvidence'];
		$strSignatory = $arrData['strSignatory'];

		$this->fpdf->SetTitle('Daily Time Record Adjustment Slip');
		$this->fpdf->SetLeftMargin(20);
		$this->fpdf->SetRightMargin(20);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on",10);
		$this->fpdf->AddPage('P','','A4');
		$this->fpdf->SetFont('Arial','B',12);
		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(0,6,'      Department of Science and Technology - X','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(0,6,'       Human Resource Management Information System','',0,'C');
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial','B',11);
		$this->fpdf->Cell(0,6,'       DAILY TIME RECORD ADJUSTMENT SLIP','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->Ln(10);
		$arrDetails=$this->empInfo();
		foreach($arrDetails as $row)
			{
				$this->fpdf->SetFont('Arial', "", 10);		
				$this->fpdf->Cell(15, 5,"Name :"  , 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "UB", 10);		
				$this->fpdf->Cell(70, 5,$row['firstname'].' '.$row['middleInitial'].' '.$row['surname']  , 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "", 10);	
				$this->fpdf->Cell(15, 5,"Position : ", 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "UB", 10);	
				$this->fpdf->Cell(20, 5,$row['positionCode'], 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "", 10);	
				$this->fpdf->Cell(30, 5,"Date : ", 0, 0, "C"); 
				$this->fpdf->SetFont('Arial', "UB", 10);	
				$this->fpdf->Cell(0, 5,"$today"."", 0, 0, "C"); 
				$this->fpdf->Ln(5);
				$this->fpdf->SetFont('Arial', "", 10);		
				$this->fpdf->Cell(30, 5,"For the month of :" , 0, 0, "C"); 
				$this->fpdf->SetFont('Arial', "U", 10);	
				//$month=$this->intToMonthFull(LTRIM($dtmMonthOf, '0'))	
				$this->fpdf->Cell(15, 5,"$month"  , 0, 0, "C"); 
				$this->fpdf->Ln(10);
			}
		// start of table
		$this->fpdf->SetFont('Arial', "B", 9);	
		$this->fpdf->Cell(50,6,'Date',"RLT",0,"C");
		$this->fpdf->Cell(40,6,'AM',"RLT",0,"C");
		$this->fpdf->Cell(40,6,'PM',"RLT",0,"C");
		$this->fpdf->Cell(40,6,'Overtime',"RT",0,"C");
		$this->fpdf->Ln(6);
		$this->fpdf->Cell(50,6,'',"RL",0,"C");
		$this->fpdf->Cell(20,6,'IN',"RLT",0,"C");
		$this->fpdf->Cell(20,6,'OUT',"RLT",0,"C");
		$this->fpdf->Cell(20,6,'IN',"RLT",0,"C");
		$this->fpdf->Cell(20,6,'OUT',"RLT",0,"C");
		$this->fpdf->Cell(20,6,'IN',"RLT",0,"C");
		$this->fpdf->Cell(20,6,'OUT',"RLT",0,"C");
		$this->fpdf->Ln(6);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(50,6,$dtmDTRupdate,"RTBL",0,"C");
		$this->fpdf->SetFont('Arial', "", 7);
		$this->fpdf->Cell(20,6,$strOldMorningIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$strOldMorningOut,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$strOldAfternoonIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$strOldAfternoonOut,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$strOldOvertimeIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$strOldOvertimeOut,"RTBL",0,"C");
		$this->fpdf->Ln(12);
		$this->fpdf->Cell(50,6,'Adjusted Entry:',"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmMorningIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmMorningOut,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmAfternoonIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmAfternoonOut,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmOvertimeIn,"RTBL",0,"C");
		$this->fpdf->Cell(20,6,$dtmOvertimeOut,"RTBL",0,"C");
		$this->fpdf->SetFont('Arial', "", 9);
		
		// $arrDetails=$this->getEmp($strSignatory);
		// $name=strtoupper($arrDetails[0]['firstname'].' '.$arrDetails[0]['middleInitial'].' '.$arrDetails[0]['surname']);
		// $this->fpdf->Cell(70,6,"$name",1,0,"C");
		// $this->fpdf->Cell(35,6,"$strEvidence",1,0,"C");
		// end of table

		$arrDetails=$this->getEmp($strSignatory);
		if(count($arrDetails) > 0):
			$name=strtoupper($arrDetails[0]['firstname'].' '.$arrDetails[0]['middleInitial'].' '.$arrDetails[0]['surname']);
		else:
			$name='';
		endif;


		$this->fpdf->Ln(30);
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->Cell(22, 5,"Submitted by :"  , 0, 0, "C"); 
		$this->fpdf->Cell(172, 5,"Certified by : ", 0, 0, "C"); 
		
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "B", 10);	
		$this->fpdf->Cell(72, 5,strtoupper($row['firstname']).' '.strtoupper($row['middleInitial']).' '.strtoupper($row['surname']), "B", 0, "C");
		$this->fpdf->Cell(22, 5,""  , 0, 0, "C");  
		$this->fpdf->Cell(55, 5,"$name", "B", 0, "C"); 
		
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->Cell(72, 5, "Signature Over Printed Name of Employee", 0, 0, "C"); 
		$this->fpdf->Cell(100, 5, "Human Resource Officer", 0, 0, "C");
		
		echo $this->fpdf->Output();
	}
	

	function empInfo()
		{
			$sql = "SELECT tblemppersonal.empNumber, tblemppersonal.surname, tblemppersonal.middleInitial, tblemppersonal.nameExtension, 
							tblemppersonal.firstname, tblemppersonal.middlename, tblplantilla.plantillaGroupCode,
							 tblplantillagroup.plantillaGroupName, tblempposition.group3, tblempposition.groupCode, tblempposition.positionCode, tblempposition.payrollGroupCode
							
							FROM tblemppersonal
							LEFT JOIN tblempposition ON tblemppersonal.empNumber = tblempposition.empNumber
							LEFT JOIN tblplantilla ON tblempposition.itemNumber = tblplantilla.itemNumber
							LEFT JOIN tblplantillagroup ON tblplantilla.plantillaGroupCode = tblplantillagroup.plantillaGroupCode
							WHERE tblemppersonal.empNumber = '".$this->session->userdata('sessEmpNo')."'";
	            		// WHERE emp_id=$empId";
	          // echo $sql;exit(1);				
			$query = $this->db->query($sql);
			return $query->result_array();	

		}	
	

}
/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */

	
	