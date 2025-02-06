<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ReportTO_rpt_model extends CI_Model
{

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		$this->load->helper(array('report_helper', 'general_helper'));
		$this->load->model(array('hr/Hr_model'));
		//ini_set('display_errors','On');
		//$this->load->model(array());
	}

	public function Header()
	{
	}

	function Footer()
	{
		$this->fpdf->SetFont('Arial', '', 7);
		$this->fpdf->Cell(50, 3, date('Y-m-d h:i A'), 0, 0, 'L');
		$this->fpdf->Cell(0, 3, "Page " . $this->fpdf->PageNo(), 0, 0, 'R');
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

	public function show_name_bold($name){
        $this->fpdf->AddFont('dejavusans', 'B', 'dejavusansb.php');

        $this->fpdf->SetFont('dejavusans', 'B', 9);

        return utf8_decode($name);
    }

	public function show_name_regular($name){
        $this->fpdf->AddFont('dejavusans', '', 'dejavusans.php');

        return utf8_decode($name);
    }


	public function get_request_signatories($requestid){
        $this->db->select("


		p0.empNumber AS SignatoryCountersignEmpNumber,
		p0.firstname AS SignatoryCountersignfirstname,
		p0.surname AS SignatoryCountersignsurname,
		p0.middleInitial AS SignatorCountersign1middleInitial,
		p0.nameExtension AS SignatorCountersign1Extension,

		p1.empNumber AS Signatory1EmpNumber,
        p1.firstname AS Signatory1firstname,
		p1.surname AS Signatory1surname,
		p1.middleInitial AS Signatory1middleInitial,
		p1.nameExtension AS Signatory1Extension,

		p2.empNumber AS Signatory2EmpNumber,
		p2.firstname AS Signatory2firstname,
		p2.surname AS Signatory2surname,
		p2.middleInitial AS Signatory2middleInitial,
		p2.nameExtension AS Signatory2Extension,

		p3.empNumber AS Signatory3EmpNumber,
		p3.firstname AS Signatory3firstname,
		p3.surname AS Signatory3surname,
		p3.middleInitial AS Signatory3middleInitial,
		p3.nameExtension AS Signatory3Extension,

		p4.empNumber AS SignatoryFinEmpNumber,
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

		$requestdetails = $this->getrequest_details($requestid);

		$office = employee_office_desc($requestdetails['empNumber']);

        $toDetails = explode(';', $requestdetails['requestDetails']);
		$requestSignatories = $this->get_request_signatories($requestdetails['requestflowid']);

		$this->fpdf->SetTitle('Travel Order');
		$this->fpdf->SetLeftMargin(15);
		$this->fpdf->SetRightMargin(15);
		$this->fpdf->SetTopMargin(15);
		$this->fpdf->SetAutoPageBreak("on", 10);
		$this->fpdf->AddPage('P', '', 'A4');

		$this->fpdf->SetFont('Arial','',11);

		$image = "assets/images/logo.png";
					$this->fpdf->Cell(10, 7, "", 0, 0, "C");
				
						$this->fpdf->Cell(10, 10, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 20), 0, 0, 'L', false);
			

		$this->fpdf->SetFont('Arial','',11);
		$this->fpdf->Cell(137,6,'       Republic of the Philippines','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','B',11);
		$this->fpdf->Cell(180,6,'       DEPARTMENT OF SCIENCE AND TECHNOLOGY - X','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',11);
		$this->fpdf->Cell(180,6,'       Cagayan de Oro City','',0,'C');
		$this->fpdf->Ln(18);
		$this->fpdf->SetFont('Arial', "B", 15);
		$this->fpdf->Cell(180, 5, "TRAVEL ORDER", 0, 0, "C");


		$this->fpdf->Ln(15);

		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(75, 5, 'LOCAL TRAVEL ORDER NO. '.$toDetails[5], 0, 0, "L");

		$this->fpdf->Cell(105, 5, 'Date Filed: '.date('F d, Y',strtotime($requestdetails['requestDate'])), 0, 0, "R");
		
		$this->fpdf->Ln(9);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(75, 5, 'Authority to Travel is hereby granted to:', 0, 0, "L");

		$this->fpdf->Ln(12);


		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(18, 5, "Name: ", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);

		$this->fpdf->Cell(30, 5, $this->show_name_regular($requestdetails['firstname'] . ' ' . 
        (!empty($requestdetails['middleInitial']) ? $requestdetails['middleInitial'] . '.' : '') . ' ' . 
        $requestdetails['surname'] . ' ' . 
        $requestdetails['nameExtension']), 0, 0, "L");

		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(72, 5, '', 0, 0, "L");
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(30, 5, "Departure Date: ", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(75, 5, date("F j, Y", strtotime($toDetails[1])), 0, 0, "L");

		$this->fpdf->Ln(7);

		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(18, 5, "Position: ", 0, 0, "L");

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(30, 5, $requestdetails['positionDesc'], 0, 0, "L");

		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(72, 5, '', 0, 0, "L");
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(30, 5, "Return Date: ", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(75, 5, date("F j, Y", strtotime($toDetails[2])), 0, 0, "L");
		

		$this->fpdf->Ln(6);

		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(18, 5, "Division: ", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(105, 5, $office, 0, 0, "L"); //Station
		$this->fpdf->SetFont('Arial', "BU", 10);

		// $this->fpdf->Cell(75, 5, "                       ", 0, 0, "L");

		$this->fpdf->Ln(7);

		
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);

		// $this->fpdf->Cell(75, 5, "                       ", 0, 0, "L");

		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(90, 5, "Destination (Place/Office)", 0, 0, "L");

		$this->fpdf->Cell(90, 5, "Purpose of the Travel", 0, 0, "L");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(90, 6, '-	'.$toDetails[0], 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->MultiCell(90, 6, '-	'.$toDetails[3], 0, "L");


		$this->fpdf->Ln(50);
		$this->fpdf->SetFont('Arial', "BI", 8);
		$this->fpdf->Cell(10, 5, "Note:", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "I", 8);

		$this->fpdf->MultiCell(170, 5, "A report of your travel must be submitted to the Agency Head/Supervising Official within 7 days completion of travel, liquidation of cash advance should be in accordance with Executive Order No. 298: Rules and Regulations and New Rates of Allowances for Official Local and Foreign Travels of Government Personnel.", 0, "L");

		$approver = $this->get_signatory($requestdetails['Signatory1']);

		$certifier = $this->get_signatory($requestdetails['Signatory3']);

		$this->fpdf->Ln(10);
        $this->fpdf->SetFont('Arial', "", 9);	
        $this->fpdf->Cell(110,7,'					Recommended by:',"",0,"L");
        $this->fpdf->Cell(92,7,'Approved by:',"",0,"L");

		$this->fpdf->Ln(10);


        $approver1 = $this->get_signatory($requestdetails['Signatory1']);
        if ($approver1) {
            $image = "uploads/employees/esignature/" . $requestdetails['Signatory1'] . ".png";
            $this->fpdf->SetFont('Arial', "I", 8);    
            // Check if the image file exists before adding it
            if (file_exists($image)) {
				$this->fpdf->Cell(23,6,'',"",0,"C");
                $this->fpdf->Cell(20, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 30), 0, 0, 'C', false);
            }
            $this->fpdf->Cell(5,6,$requestdetails['Sig1DateTime'],0,0,"L");
        } else {
            $this->fpdf->Cell(10,6,'',"",0,"C");
        }

		$this->fpdf->Cell(62,6,'',"",0,"C");

        $approver2 = $this->get_signatory($requestdetails['Signatory2']);

		if ($approver2) {
            $image = "uploads/employees/esignature/" . $requestdetails['Signatory2'] . ".png";
            $this->fpdf->SetFont('Arial', "I", 8);    
            // Check if the image file exists before adding it
            if (file_exists($image)) {
				$this->fpdf->Cell(10,6,'',"",0,"C");
                $this->fpdf->Cell(20, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 30), 0, 0, 'C', false);
            }
            $this->fpdf->Cell(8,6,$requestdetails['Sig2DateTime'],0,0,"L");
        } else {
            $this->fpdf->Cell(10,6,'',"",0,"C");
        }


		$this->fpdf->Ln(8);


		if ($approver) {
			$this->fpdf->Cell(
				75, 
				5, 
				$this->show_name_bold(strtoupper($approver['firstname'] . ' ' . 
				(!empty($approver['middleInitial']) ? $approver['middleInitial'] . '.' : '') . ' ' . 
				$approver['surname'] . ' ' . 
				$approver['nameExtension'])), 
				0, 
				0, 
				"C"
			);
		}else{
			$this->fpdf->Cell(
				75, 
				5, 
				$this->show_name_bold(strtoupper($requestSignatories['Signatory1firstname'] . ' ' . 
				(!empty($requestSignatories['middleInitial']) ? $requestSignatories['Signatory1middleInitial'] . '.' : '') . ' ' . 
				$requestSignatories['Signatory1surname'] . ' ' . 
				$requestSignatories['Signatory1Extension'])), 
				0, 
				0, 
				"C"
			);
		}
		

		if ($certifier) {
			$this->fpdf->Cell(
				55, 
				5, 
				$this->show_name_bold(strtoupper($certifier['firstname'] . ' ' . 
				(!empty($certifier['middleInitial']) ? $certifier['middleInitial'] . '.' : '') . ' ' . 
				$certifier['surname'] . ' ' . 
				$certifier['nameExtension'])), 
				0, 
				0, 
				"C"
			);
		}else{
			$this->fpdf->Cell(120, 5, $this->show_name_bold(strtoupper($requestSignatories['Signatory2firstname'] . ' ' . (!empty($requestSignatories['Signatory2middleInitial']) ? $requestSignatories['Signatory2middleInitial'] . '.' : '') . ' ' . $requestSignatories['Signatory2surname'] . ' ' . $requestSignatories['Signatory2Extension'])), 0, 0, "C");
		}

		$this->fpdf->Ln(5);
        $this->fpdf->SetFont('Arial', "", 8.5);	

		if ($approver) {
			$this->fpdf->Cell(75, 5, employee_details($approver['empNumber'])[0]['positionDesc'], 0, 0, "C");
		}else{
			$this->fpdf->Cell(75, 5, employee_details($requestSignatories['Signatory1EmpNumber'])[0]['positionDesc'], 0, 0, "C");
		}

		if ($certifier) {
			$this->fpdf->Cell(75, 5, employee_details($certifier['empNumber'])[0]['positionDesc'], 0, 0, "C");
		}else{
			$this->fpdf->Cell(120, 5, employee_details($requestSignatories['Signatory2EmpNumber'])[0]['positionDesc'], 0, 0, "C");
		}

		echo $this->fpdf->Output();
	}
}
/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */
