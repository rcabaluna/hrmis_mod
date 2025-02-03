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


	public function getrequest_details($requestid){

        $this->db->select('req.*, pers.surname, pers.firstname, pers.middleInitial, pers.middlename, pers.nameExtension, pos.positionDesc, emppos.actualSalary');
        $this->db->from('tblemprequest AS req');
        $this->db->join('tblemppersonal AS pers', 'pers.empNumber = req.empNumber');
        $this->db->join('tblempposition AS emppos', 'emppos.empNumber = req.empNumber');
        $this->db->join('tblposition AS pos', 'pos.positionCode = emppos.positionCode');
        $this->db->where('req.requestID', $requestid);

        $query = $this->db->get();
        return $query->row_array();
    }

	public function get_request_signatories($requestid){
        $this->db->select("


		p0.firstname AS SignatoryCountersignfirstname,
		p0.surname AS SignatoryCountersignsurname,
		p0.middleInitial AS SignatorCountersign1middleInitial,
		p0.nameExtension AS SignatorCountersign1Extension,

        p1.firstname AS Signatory1firstname,
		p1.surname AS Signatory1surname,
		p1.middleInitial AS Signatory1middleInitial,
		p1.nameExtension AS Signatory1Extension,

		p2.firstname AS Signatory2firstname,
		p2.surname AS Signatory2surname,
		p2.middleInitial AS Signatory2middleInitial,
		p2.nameExtension AS Signatory2Extension,

		p3.firstname AS Signatory3firstname,
		p3.surname AS Signatory3surname,
		p3.middleInitial AS Signatory3middleInitial,
		p3.nameExtension AS Signatory3Extension,

		p4.firstname AS SignatoryFinfirstname,
		p4.surname AS SignatoryFinsurname,
		p4.middleInitial AS SignatoryFinmiddleInitial,
		p4.nameExtension AS SignatoryFinExtension

    ");
    $this->db->from('tblrequestflow r');
    $this->db->join('tblemppersonal p0', "p0.empNumber = SUBSTRING_INDEX(r.SignatoryCountersign, ';', -1)", 'left');
    $this->db->join('tblemppersonal p1', "p1.empNumber = SUBSTRING_INDEX(r.Signatory1, ';', -1)", 'left');
    $this->db->join('tblemppersonal p2', "p2.empNumber = SUBSTRING_INDEX(r.Signatory2, ';', -1)", 'left');
    $this->db->join('tblemppersonal p3', "p3.empNumber = SUBSTRING_INDEX(r.Signatory3, ';', -1)", 'left');
    $this->db->join('tblemppersonal p4', "p4.empNumber = SUBSTRING_INDEX(r.SignatoryFin, ';', -1)", 'left');

    $this->db->where('r.reqID', $requestid);

    $query = $this->db->get();
    return $query->row_array();
    }

	public function get_signatory($empNumber = ''){

        $this->db->select('empNumber, surname, firstname, middlename, middleInitial, nameExtension');
        $this->db->from('tblemppersonal');
        $this->db->where('empNumber', $empNumber);
        $query = $this->db->get();

        return $query->row_array(); // Return a single row as an associative array
        
    }


	
	function generate($requestid)
	{
		$reqdetails = $this->getrequest_details($requestid);
        $obdetails = explode(';', $reqdetails['requestDetails']);

		var_dump($obdetails);

		$requestSignatories = $this->get_request_signatories($reqdetails['requestflowid']);

		// $strOBtype=$arrData['strOBtype'];
		$dtmOBrequestdate = $obdetails[1] == '' ? '' : date("F j, Y",strtotime($obdetails[1]));
		$dtmOBdatefrom = $obdetails[2] == '' ? '' : date("F j, Y",strtotime($obdetails[2]));
		$dtmOBdateto = $obdetails[3] == '' ? '' : date("F j, Y",strtotime($obdetails[3]));
		$dtmTimeFrom = $obdetails[4];
		$dtmTimeTo = $obdetails[5];
		$strDestination = $obdetails[6];
		$strPurpose = $obdetails[7];
		$strOfficial = "";
		$strPersonal = ""; 
		$strEmpNum = $reqdetails['empNumber'];

		if ($obdetails[0]=='Official')
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
				$this->fpdf->Ln(10);
				$this->fpdf->SetFont('Arial', "B", 10);		
				$this->fpdf->Cell(100, 5,""  , 0, 0, "C"); 
				$this->fpdf->Cell(100, 5," ", 0, 0, "C"); 
				$this->fpdf->Ln(1);
				$this->fpdf->Cell(
					75, 
					5, 
					strtoupper($reqdetails['firstname'] . ' ' . 
					(!empty($row['middleInitial']) ? $reqdetails['middleInitial'] . '.' : '') . ' ' . 
					$reqdetails['surname'] . ' ' . 
					$reqdetails['nameExtension']), 
					0, 
					0, 
					"C"
				);
				
				$this->fpdf->Cell(110, 5,strtoupper($reqdetails['positionDesc']), 0, 0, "C"); 

				$this->fpdf->Ln(5);
				$this->fpdf->SetFont('Arial', "", 10);		
				$this->fpdf->Cell(70, 5, "Name", "T", 0, "C"); 
				$this->fpdf->Cell(25, 5, "", 0, 0, "C"); 
				$this->fpdf->Cell(70, 5, "Position", "T", 0, "C"); 
				$this->fpdf->Ln(10);
				$this->fpdf->SetFont('Arial', "B", 10);		

				if ($reqdetails['empNumber']) {
					$image = "uploads/employees/esignature/".$reqdetails['empNumber'].".png";
					$this->fpdf->Cell(25, 5, "", 0, 0, "C"); 
					$this->fpdf->Cell(40, 7, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 30), 0, 0, 'C', false );		
				}
				$this->fpdf->Cell(100, 5, "", 0, 0, "C"); 
				$this->fpdf->Ln(1);
				$this->fpdf->Cell(75, 5,"", 0, 0, "L"); 
				$this->fpdf->Cell(110, 5,$dtmOBrequestdate, 0, 0, "C"); 

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
		$this->fpdf->Cell(10, 5, '  ', 0, 0, "C");

		$this->fpdf->Ln(1); 
		$this->fpdf->Cell(34, 5,"", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);

		if ($dtmOBdatefrom == $dtmOBdateto) {
			$this->fpdf->Cell(20, 3,"$dtmOBdatefrom", 0, 0, "L"); 
		}else{
			$this->fpdf->Cell(20, 3,"$dtmOBdatefrom".' - '."$dtmOBdateto", 0, 0, "L"); 
		}
		$this->fpdf->Cell(20, 5,"", 0, 0, "C");
		$this->fpdf->Cell(135, 3,"$dtmTimeFrom".' - '."$dtmTimeTo", 0, 0, "C"); 



		$this->fpdf->Ln(15);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(2, 5,"", 0, 0, "L"); 
		$this->fpdf->Cell(92, 5,"APPROVED BY:", 0, 0, "L"); 
		$this->fpdf->Cell(100, 5,"CERTIFIED BY:", 0, 0, "L");

		$this->fpdf->Ln(15);

		$image = "uploads/employees/esignature/".$reqdetails['Signatory1'].".png";

		if (file_exists($image)) {
			

            $this->fpdf->SetFont('Arial', "I", 8);	
            $this->fpdf->Cell(22,6,'',0,"C");
            $this->fpdf->Cell(28, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 50), 0, 0, 'C', false );
            $this->fpdf->Cell(40,6,$reqdetails['Sig1DateTime'],0,0,"L");
		}else{
            $this->fpdf->Cell(98,6,'',"",0,"C");
		}

		$image = "uploads/employees/esignature/".$reqdetails['SignatoryFin'].".png";

		if (file_exists($image)) {
			$approver1 = $this->get_signatory($reqdetails['SignatoryFin']);

            $this->fpdf->SetFont('Arial', "I", 8);	
            $this->fpdf->Cell(22,6,'',0,"C");
            $this->fpdf->Cell(28, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 50), 0, 0, 'C', false );
            $this->fpdf->Cell(40,6,$reqdetails['SigFinDateTime'],0,0,"L");
		}else{
            $this->fpdf->Cell(98,6,'',"",0,"C");
		}

		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "B", 10);		
		$this->fpdf->Cell(100, 5,"", 0, 0, "C"); 
		$this->fpdf->Cell(100, 5, "", 0, 0, "C"); 
		$this->fpdf->Ln(5);

		$approver = $this->get_signatory($reqdetails['Signatory1']);
		$certifier = $this->get_signatory($reqdetails['SignatoryFin']);


		if ($approver) {
			$this->fpdf->Cell(
				75, 
				5, 
				strtoupper($approver['firstname'] . ' ' . 
				(!empty($approver['middleInitial']) ? $approver['middleInitial'] . '.' : '') . ' ' . 
				$approver['surname'] . ' ' . 
				$approver['nameExtension']), 
				0, 
				0, 
				"C"
			);
		}else{
			$this->fpdf->Cell(
				75, 
				5, 
				strtoupper($requestSignatories['Signatory1firstname'] . ' ' . 
				(!empty($requestSignatories['middleInitial']) ? $requestSignatories['Signatory1middleInitial'] . '.' : '') . ' ' . 
				$requestSignatories['Signatory1surname'] . ' ' . 
				$requestSignatories['Signatory1Extension']), 
				0, 
				0, 
				"C"
			);
		}

		if ($certifier) {
			$this->fpdf->Cell(
				110, 
				5, 
				strtoupper($certifier['firstname'] . ' ' . 
				(!empty($certifier['middleInitial']) ? $certifier['middleInitial'] . '.' : '') . ' ' . 
				$certifier['surname'] . ' ' . 
				$certifier['nameExtension']), 
				0, 
				0, 
				"C"
			);
		}else{
			$this->fpdf->Cell(
				110, 
				5, 
				strtoupper($requestSignatories['SignatoryFinfirstname'] . ' ' . 
				(!empty($requestSignatories['SignatoryFinmiddleInitial']) ? $requestSignatories['SignatoryFinmiddleInitial'] . '.' : '') . ' ' . 
				$requestSignatories['SignatoryFinsurname'] . ' ' . 
				$requestSignatories['SignatoryFinExtension']), 
				0, 
				0, 
				"C"
			);
		}

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

		 if ($reqdetails['SigFinDateTime'] != NULL) {
			$formattedDate = date('F d, Y', strtotime($reqdetails['SigFinDateTime']));
		} else {
			$formattedDate = '';
		}
		
		$this->fpdf->Cell(260, 4, "Date :  ".$formattedDate, 0, 0, "C"); 
		$this->fpdf->Ln(0);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(95, 5, "", 0, 0, "C"); 
		$this->fpdf->Cell(20, 5,"___________________________________", 0, 0, "L"); 
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "BI", 8);
		$this->fpdf->Cell(200, 5, "", 0, 0, "C");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 8);
			
		echo $this->fpdf->Output();
	}
		
}


/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */

	
	