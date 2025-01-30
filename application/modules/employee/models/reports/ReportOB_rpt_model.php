<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ReportOB_rpt_model extends CI_Model {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		$this->load->helper('report_helper');	
		//ini_set('display_errors','On');
		//$this->load->model(array());
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
		$strOBtype=$arrData['strOBtype'];
		$dtmOBrequestdate = $arrData['dtmOBrequestdate'] == '' ? '' : date("F j, Y",strtotime($arrData['dtmOBrequestdate']));
		$dtmOBdatefrom = $arrData['dtmOBdatefrom'] == '' ? '' : date("F j, Y",strtotime($arrData['dtmOBdatefrom']));
		$dtmOBdateto = $arrData['dtmOBdateto'] == '' ? '' : date("F j, Y",strtotime($arrData['dtmOBdateto']));
		$dtmTimeFrom = $arrData['dtmTimeFrom'];
		$dtmTimeTo = $arrData['dtmTimeTo'];
		$strDestination = $arrData['strDestination'];
		$strPurpose = $arrData['strPurpose'];
		$strOfficial = "";
		$strPersonal = ""; 
		$strEmpNum = $arrData['strEmpNum'];

		if ($strOBtype=='Official')
			$strOfficial = "x";
		else
			$strPersonal = "x";

		$this->fpdf->SetTitle('Official Business');
		$this->fpdf->SetLeftMargin(25);
		$this->fpdf->SetRightMargin(25);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on",10);
		$this->fpdf->AddPage('P','','A4');
		$this->fpdf->SetFont('Arial','B',12);
		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial','',11);
		$this->fpdf->Cell(0,6,'       Republic of the Philippines','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','B',11);
		$this->fpdf->Cell(0,6,'       DEPARTMENT OF SCIENCE AND TECHNOLOGY','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',11);
		$this->fpdf->Cell(0,6,'       Regional Office X','',0,'C');
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial','B',11);
		$this->fpdf->Cell(0,6,'PERSONNEL TRAVEL PASS','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',10);

		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(75, 5,"[", 0, 0, "R"); 
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(5, 5,"$strOfficial" , 0, 0, "L"); 
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(10, 5,"]  Official" , 0, 0, "L"); 
		
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(75, 5,"[", 0, 0, "R"); 
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(5, 5,"$strPersonal", 0, 0, "L");  
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(10, 5,"]  Personal" , 0, 0, "L");

		$this->fpdf->Ln(10);

		$arrDetails=$this->empInfo($strEmpNum);
		$superVisor = $this->immediateSupervisor($strEmpNum);
		$certifier = $this->certifier($strEmpNum);


		foreach($arrDetails as $row)
			{

				$this->fpdf->Ln(10);
				$this->fpdf->SetFont('Arial', "B", 10);		
				$this->fpdf->Cell(100, 5,""  , 0, 0, "C"); 
				$this->fpdf->Cell(100, 5," ", 0, 0, "C"); 
				$this->fpdf->Ln(1);
				$this->fpdf->Cell(
					75, 
					5, 
					$row['firstname'] . ' ' . 
					(!empty($row['middleInitial']) ? $row['middleInitial'] . '.' : '') . ' ' . 
					$row['surname'] . ' ' . 
					$row['nameExtension'], 
					0, 
					0, 
					"C"
				);
				
				$this->fpdf->Cell(110, 5,$row['positionDesc'], 0, 0, "C"); 

				$this->fpdf->Ln(5);
				$this->fpdf->SetFont('Arial', "", 10);		
				$this->fpdf->Cell(70, 5, "Name", "T", 0, "C"); 
				$this->fpdf->Cell(25, 5, "", 0, 0, "C"); 
				$this->fpdf->Cell(70, 5, "Position / Office", "T", 0, "C"); 
				$this->fpdf->Ln(10);
				$this->fpdf->SetFont('Arial', "B", 10);		
				$this->fpdf->Cell(100, 5,'', 0, 0, "C"); 
				$this->fpdf->Cell(100, 5, "", 0, 0, "C"); 
				$this->fpdf->Ln(1);
				$this->fpdf->Cell(75, 5,"", 0, 0, "L"); 
				$this->fpdf->Cell(110, 5,$dtmOBrequestdate, 0, 0, "C"); 
			}

		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->Cell(70, 5, "Signature", "T", 0, "C"); 
		$this->fpdf->Cell(25, 5, "", 0, 0, "C"); 
		$this->fpdf->Cell(70, 5, "Date of Application", "T", 0, "C"); 
		  
		$this->fpdf->Ln(15);   
		$this->fpdf->SetFont('Arial', "", 10);	
		$this->fpdf->Cell(2, 5,"", 0, 0, "L"); 	
		$this->fpdf->Cell(28, 5,"DESTINATION :", 0, 0, "L"); 
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->MultiCell(155, 5,"$strDestination", 0, 'J', 0);
		$this->fpdf->Cell(28, 5,"", 0, 0, "L");
		$this->fpdf->Cell(100, 1,"____________________________________________________________________", 0, 0, "L"); 

		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "", 10);	
		$this->fpdf->Cell(2, 5,"", 0, 0, "L"); 	
		$this->fpdf->Cell(28, 5,"PURPOSE :", 0, 0, "L"); 
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->MultiCell(140, 5,"$strPurpose", 0, 'J', 0);
		  
		$this->fpdf->Cell(28, 5,"", 0, 0, "L");
		$this->fpdf->Cell(100, 1,"____________________________________________________________________", 0, 0, "L");  

		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(2, 5,"", 0, 0, "L"); 		
		$this->fpdf->Cell(25, 5,"Date of Travel", 0, 0, "L"); 
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(40, 5,'    :', 0, 0, "L");

		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->Cell(80, 5,"Time of Travel :", 0, 0, "C"); 
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(10, 5, ' - ', 0, 0, "C");

		$this->fpdf->Ln(1); 
		$this->fpdf->Cell(34, 5,"", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "U", 10);
		$this->fpdf->Cell(20, 3,"$dtmOBdatefrom".' - '."$dtmOBdateto", 0, 0, "L"); 
		$this->fpdf->Cell(20, 5,"", 0, 0, "C");
		$this->fpdf->Cell(135, 3,"$dtmTimeFrom".' - '."$dtmTimeTo", 0, 0, "C"); 

		$this->fpdf->Ln(15);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(2, 5,"", 0, 0, "L"); 		
		$this->fpdf->Cell(10, 5,"APPROVED BY:", 0, 0, "L"); 
		$this->fpdf->Cell(195, 5,"CERTIFIED BY:", 0, 0, "C");
		
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(100, 5,"", 0, 0, "C"); 
		$this->fpdf->Cell(100, 5, "", 0, 0, "C"); 
		$this->fpdf->Ln(5);
		$this->fpdf->Cell(
			75, 
			5, 
			$superVisor['firstname'] . ' ' . 
			(!empty($superVisor['middleInitial']) ? $superVisor['middleInitial'] . '.' : '') . ' ' . 
			$superVisor['surname'] . ' ' . 
			$superVisor['nameExtension'], 
			0, 
			0, 
			"C"
		);
		
		$this->fpdf->Cell(
			110, 
			5, 
			$certifier['firstname'] . ' ' . 
			(!empty($certifier['middleInitial']) ? $certifier['middleInitial'] . '.' : '') . ' ' . 
			$certifier['surname'] . ' ' . 
			$certifier['nameExtension'], 
			0, 
			0, 
			"C"
		);
		

		$this->fpdf->Ln(1);
		$this->fpdf->Cell(75, 5,"___________________________________", 0, 0, "C"); 
		$this->fpdf->Cell(110, 5,"___________________________________", 0, 0, "C"); 		
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);		
		$this->fpdf->Cell(75, 5, "Immediate Supervisor", 0, 0, "C"); 
		$this->fpdf->Cell(110, 5, "Human Resource Officer", 0, 0, "C"); 
		$this->fpdf->Cell(100, 5, "", 0, 0, "C"); 
	 
 		$this->fpdf->Ln(5);
 		$this->fpdf->SetFont('Arial', "", 10);
 		$this->fpdf->Ln(5);
		$this->fpdf->Cell(260, 4, "Date :  "."$dtmOBrequestdate", 0, 0, "C"); 
		$this->fpdf->Ln(0);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(95, 5, "", 0, 0, "C"); 
		$this->fpdf->Cell(20, 5,"___________________________________", 0, 0, "L"); 
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "BI", 8);
		$this->fpdf->Cell(200, 5, "", 0, 0, "C");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 8);
		$this->fpdf->Cell(0, 4, "**************** NO SIGNATURE NEEDED. THIS DOCUMENT HAS BEEN APPROVED ONLINE ****************", 0, 1, "C");		
			
		echo $this->fpdf->Output();
	}
	
	function empInfo($empnum)
	{
		$sql = "SELECT tblemppersonal.empNumber, tblemppersonal.surname, tblemppersonal.middleInitial, tblemppersonal.nameExtension, 
						tblemppersonal.firstname, tblemppersonal.middlename, tblplantilla.plantillaGroupCode,
							tblplantillagroup.plantillaGroupName, tblempposition.group3, tblempposition.groupCode, tblempposition.positionCode, tblempposition.payrollGroupCode,
							tblposition.positionDesc
						
						FROM tblemppersonal
						LEFT JOIN tblempposition ON tblemppersonal.empNumber = tblempposition.empNumber
						LEFT JOIN tblposition ON tblempposition.positionCode = tblposition.positionCode
						LEFT JOIN tblplantilla ON tblempposition.itemNumber = tblplantilla.itemNumber
						LEFT JOIN tblplantillagroup ON tblplantilla.plantillaGroupCode = tblplantillagroup.plantillaGroupCode
						WHERE tblemppersonal.empNumber = '".$empnum."'";
						// '".$this->session->userdata('sessEmpNo')."'";
					// WHERE emp_id=$empId";
			// echo $sql;exit(1);				
		$query = $this->db->query($sql);
		return $query->result_array();	

	}	

	function immediateSupervisor($empnum)
	{	
		for ($i=5; $i >=1; $i--) { 
			$sql = "SELECT tblgroup".$i.".empNumber, tblemppersonal.surname, tblemppersonal.middleInitial, tblemppersonal.nameExtension, 
						tblemppersonal.firstname FROM tblempposition
			LEFT JOIN tblgroup".$i." ON tblgroup".$i.".group".$i."Code = tblempposition.group".$i."
			JOIN tblemppersonal ON tblemppersonal.empNumber = tblgroup".$i.".empNumber
			WHERE tblempposition.empNumber = '".$empnum."'";
			$query = $this->db->query($sql);
			
			$result = $query->row_array();

			if ($result['empNumber']) {
				return $result;
			}
		}
	}

	function certifier($empnum){

		$assignedTo = '';
		
		for ($i=5; $i >=1; $i--) { 
			$sql = "SELECT tblgroup".$i.".empNumber, tblemppersonal.surname, tblemppersonal.middleInitial, tblemppersonal.nameExtension, 
						tblemppersonal.firstname, group".$i." AS assignedTo  FROM tblempposition
			LEFT JOIN tblgroup".$i." ON tblgroup".$i.".group".$i."Code = tblempposition.group".$i."
			JOIN tblemppersonal ON tblemppersonal.empNumber = tblgroup".$i.".empNumber
			WHERE tblempposition.empNumber = '".$empnum."'";
			
			
			$query = $this->db->query($sql);
			
			$result = $query->row_array();
			
			if ($result['empNumber']) {
				$assignedTo = $result['assignedTo'];
				break;
			}

			
		}

		$sql = "SELECT surname, middleInitial, nameExtension, firstname, middlename
				FROM tblemppersonal
				WHERE empNumber = (
					SELECT SUBSTRING_INDEX(SignatoryFin, ';', -1) AS LastValue
					FROM hrmis.tblrequestflow
					WHERE Applicant LIKE '%".$assignedTo."%'
					AND isActive = 1
					AND RequestType = 'OB'
				)";

		$query = $this->db->query($sql);
		
		return $query->row_array();

	}
	
}


/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */

	
	