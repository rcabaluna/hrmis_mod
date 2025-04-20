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

	public function getrequest_details($requestid){

        $this->db->select('req.*, pers.surname, pers.firstname, pers.middleInitial, pers.middlename, pers.nameExtension, pos.positionDesc, emppos.actualSalary');
        $this->db->from('tblemprequest AS req');
        $this->db->join('tblemppersonal AS pers', 'pers.empNumber = req.empNumber', 'LEFT');
        $this->db->join('tblempposition AS emppos', 'emppos.empNumber = req.empNumber', 'LEFT');
        $this->db->join('tblposition AS pos', 'pos.positionCode = emppos.positionCode', 'LEFT');
        $this->db->where('req.requestID', $requestid);

        $query = $this->db->get();
        return $query->row_array();
    }

	
	function generate($requestid)
	{

		$reqdetails = $this->getrequest_details($requestid);
		$dtrupdate_details = explode(';', $reqdetails['requestDetails']);
		

		$today =  date("F j, Y",strtotime(date("Y-m-d")));
		$dtmDTRupdate = $dtrupdate_details[1]==''?'':date("F j, Y",strtotime($dtrupdate_details[1]));
		$month = $dtrupdate_details[1]==''?'':date("F",strtotime($dtrupdate_details[1]));

	
		// old
		$strOldMorningIn= ($dtrupdate_details[2] != "") ?  date("h:i A", strtotime($dtrupdate_details[2])) : '-';
		$strOldMorningOut= ($dtrupdate_details[3] != "") ?  date("h:i A", strtotime($dtrupdate_details[3])) : '-';
		$strOldAfternoonIn= ($dtrupdate_details[4] != "") ?  date("h:i A", strtotime($dtrupdate_details[4])) : '-';
		$strOldAfternoonOut= ($dtrupdate_details[5] != "") ?  date("h:i A", strtotime($dtrupdate_details[5])) : '-';
		$strOldOvertimeIn= ($dtrupdate_details[6] != "") ?   date("h:i A", strtotime($dtrupdate_details[6])) : '-';
		$strOldOvertimeOut= ($dtrupdate_details[7] != "") ?  date("h:i A", strtotime($dtrupdate_details[7])) : '-';
		// new
		$dtmMorningIn = ($dtrupdate_details[8] != "") ? date("h:i A", strtotime($dtrupdate_details[8])) : '-';
		$dtmMorningOut = ($dtrupdate_details[9] != "") ? date("h:i A", strtotime($dtrupdate_details[9])) : '-';
		$dtmAfternoonIn = ($dtrupdate_details[10] != "") ? date("h:i A", strtotime($dtrupdate_details[10])) : '-';
		$dtmAfternoonOut = ($dtrupdate_details[11] != "") ? date("h:i A", strtotime($dtrupdate_details[11])) : '-';
		$dtmOvertimeIn = ($dtrupdate_details[12] != "") ? date("h:i A", strtotime($dtrupdate_details[12])) : '-';
		$dtmOvertimeOut = ($dtrupdate_details[13] != "") ? date("h:i A", strtotime($dtrupdate_details[13])) : '-';

		

		$strReason = $dtrupdate_details[14];
		// $dtmMonthOf = $arrData['dtmMonthOf'];
		
		$strEvidence = $dtrupdate_details[16];
		$strSignatory = $dtrupdate_details[17];

	

		$this->fpdf->SetTitle('Daily Time Record Adjustment Slip');
		$this->fpdf->SetLeftMargin(20);
		$this->fpdf->SetRightMargin(20);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on",10);
		$this->fpdf->AddPage('P','','A4');
		$this->fpdf->SetFont('Arial','B',12);
		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial','',11);

		$image = "assets/images/logo.png";
		$this->fpdf->Cell(10, 7, "", 0, 0, "C");
				
		$this->fpdf->Cell(10, 10, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 15), 0, 0, 'L', false);
			


		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(139,6,'Republic of the Philippines','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','B',10);
		$this->fpdf->Cell(180,6,'DEPARTMENT OF SCIENCE AND TECHNOLOGY','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(180,6,'Regional Office No. X','',0,'C');
		$this->fpdf->Ln(13);
		$this->fpdf->SetFont('Arial', "B", 13);
		$this->fpdf->Cell(180, 5, "DAILY TIME RECORD ADJUSTMENT SLIP", 0, 0, "C");

		$this->fpdf->Ln(15);


		$arrDetails=$this->empInfo();
		foreach($arrDetails as $row)
			{
				$this->fpdf->SetFont('Arial', "", 10);		
				$this->fpdf->Cell(15, 5,"Name :"  , 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "UB", 10);		
				$this->fpdf->Cell(70, 5,strtoupper($row['firstname'].' '.$row['middleInitial'].' '.$row['surname'] ) , 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "", 10);	
				$this->fpdf->Cell(15, 5,"Position : ", 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "UB", 10);	
				$this->fpdf->Cell(20, 5,$row['positionCode'], 0, 0, "L"); 
				$this->fpdf->SetFont('Arial', "", 10);	
				$this->fpdf->Cell(30, 5,"Date Requested: ", 0, 0, "C"); 
				$this->fpdf->SetFont('Arial', "UB", 10);	
				$this->fpdf->Cell(0, 5,date('F d, Y', strtotime($reqdetails['requestDate'])), 0, 0, "C"); 
				$this->fpdf->Ln(5);
				$this->fpdf->SetFont('Arial', "", 10);		
				// $this->fpdf->Cell(30, 5,"For the month of :" , 0, 0, "C"); 
				// $this->fpdf->SetFont('Arial', "U", 10);	
				//$month=$this->intToMonthFull(LTRIM($dtmMonthOf, '0'))	
				// $this->fpdf->Cell(15, 5,"$month"  , 0, 0, "C"); 
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
		$this->fpdf->SetFont('Arial', "", 10);

		$this->fpdf->Ln(12);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(50,6,'Reason:',0 ,0,"L");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(170,6,"			".$strReason,0 ,0,"L");

		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(50,6,'Evidence:',0 ,0,"L");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(170,6,"			".$strEvidence,0 ,0,"L");

		
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

	
	