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
        $this->db->join('tblemppersonal AS pers', 'pers.empNumber = req.empNumber', 'left');
        $this->db->join('tblempposition AS emppos', 'emppos.empNumber = req.empNumber', 'left');
        $this->db->join('tblposition AS pos', 'pos.positionCode = emppos.positionCode', 'left');
        $this->db->where('req.requestID', $requestid);

        $query = $this->db->get();
        return $query->row_array();
    }

	public function getrequesters_info($empNumber) {
        // Building the query
        $this->db->select('
            a.firstname, 
            a.middleInitial, 
            a.surname, 
            a.nameExtension, 
            c.positionAbb, 
            c.positionDesc, 
            COALESCE(g5.group5Name, g4.group4Name, g3.group3Name, g2.group2Name, g1.group1Name) AS groupName
        ');
        
        $this->db->from('tblemppersonal a');
        
        // Joins
        $this->db->join('tblempposition b', 'b.empNumber = a.empNumber', 'left');
        $this->db->join('tblposition c', 'c.positionCode = b.positionCode', 'left');
        $this->db->join('tblgroup5 g5', 'g5.group5Code = b.group5', 'left');
        $this->db->join('tblgroup4 g4', 'g4.group4Code = b.group4', 'left');
        $this->db->join('tblgroup3 g3', 'g3.group3Code = b.group3', 'left');
        $this->db->join('tblgroup2 g2', 'g2.group2Code = b.group2', 'left');
        $this->db->join('tblgroup1 g1', 'g1.group1Code = b.group1', 'left');
        
        // Where condition for empNumber
        $this->db->where('a.empNumber', $empNumber);
        
        // Execute the query
        $query = $this->db->get();
        
        // Return the result
        if ($query->num_rows() > 0) {
            return $query->row_array(); // If you want a single row of data
        } else {
            return null; // No results found
        }
    }


	public function getto_funding_details($requestid){
		$this->db->where('requestID', $requestid);
		$query = $this->db->get('tblempto_req_details');
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
		$fundingdetails = $this->getto_funding_details($requestid);

		$actual_accommodation_general = $actual_accommodation_project = $actual_accommodation_others = "";
		$actual_meals_general = $actual_meals_project = $actual_meals_others = "";
		$perdiem_accommodation_general = $perdiem_accommodation_project = $perdiem_accommodation_others = "";
		$perdiem_meals_general = $perdiem_meals_project = $perdiem_meals_others = "";
		$perdiem_incidental_general = $perdiem_incidental_project = $perdiem_incidental_others = "";
		$transport_official_general	= $transport_official_project = $transport_official_others	= "";
		$transport_public_general	= $transport_public_project = $transport_public_others	= "";
		$others_details_remarks_general	= $others_details_remarks_project = $others_details_remarks_others	= "";






		if ($fundingdetails['actual'] == 'general') {
			$actual_accommodation_general = 4;
		} elseif ($fundingdetails['actual'] == 'project') {
			$actual_accommodation_project = 4;
		} elseif ($fundingdetails['actual'] == 'others') {
			$actual_accommodation_others = 4;
		}

		if ($fundingdetails['perdiem_accomodation'] == 'general') {
			$perdiem_accommodation_general = 4;
		} elseif ($fundingdetails['perdiem_accomodation'] == 'project') {
			$perdiem_accommodation_project = 4;
		} elseif ($fundingdetails['perdiem_accomodation'] == 'others') {
			$perdiem_accommodation_others = 4;
		}

		if ($fundingdetails['perdiem_meals'] == 'general') {
			$perdiem_meals_general = 4;
		} elseif ($fundingdetails['perdiem_meals'] == 'project') {
			$perdiem_meals_project = 4;
		} elseif ($fundingdetails['perdiem_meals'] == 'others') {
			$perdiem_meals_others = 4;
		}

		if ($fundingdetails['perdiem_incidental'] == 'general') {
			$perdiem_incidental_general = 4;
		} elseif ($fundingdetails['perdiem_incidental'] == 'project') {
			$perdiem_incidental_project = 4;
		} elseif ($fundingdetails['perdiem_incidental'] == 'others') {
			$perdiem_incidental_others = 4;
		}

		if ($fundingdetails['transport_official'] == 'general') {
			$transport_official_general = 4;
		} elseif ($fundingdetails['transport_official'] == 'project') {
			$transport_official_project = 4;
		} elseif ($fundingdetails['transport_official'] == 'others') {
			$transport_official_others = 4;
		}

		if ($fundingdetails['transport_public'] == 'general') {
			$transport_public_general = 4;
		} elseif ($fundingdetails['transport_public'] == 'project') {
			$transport_public_project = 4;
		} elseif ($fundingdetails['transport_public'] == 'others') {
			$transport_public_others = 4;
		}

		if ($fundingdetails['others_details'] == 'general') {
			$others_details_remarks_general = $fundingdetails['others_details_remarks'];
		} elseif ($fundingdetails['others_details'] == 'project') {
			$others_details_remarks_project = $fundingdetails['others_details_remarks'];
		} elseif ($fundingdetails['others_details'] == 'others') {
			$others_details_remarks_others = $fundingdetails['others_details_remarks'];
		}


		




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
				
		$this->fpdf->Cell(10, 10, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 15), 0, 0, 'L', false);
			


		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(137,6,'       Republic of the Philippines','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','B',10);
		$this->fpdf->Cell(180,6,'       DEPARTMENT OF SCIENCE AND TECHNOLOGY','',0,'C');
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial','',10);
		$this->fpdf->Cell(180,6,'       Regional Office No. X','',0,'C');
		$this->fpdf->Ln(13);
		$this->fpdf->SetFont('Arial', "B", 13);
		$this->fpdf->Cell(180, 5, "TRAVEL ORDER", 0, 0, "C");


		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(170, 5, date('F d, Y',strtotime($requestdetails['requestDate'])), 0, 0, "R");

		$this->fpdf->Ln(0.2);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(180, 5, "_______________________", 0, 0, "R");

		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(165, 5, "(Date)", 0, 0, "R");

		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(45, 5, "LOCAL TRAVEL ORDER NO.	", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(5, 5,"  ".$toDetails[5], 0, 0, "L");


		$this->fpdf->Ln(0.2);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(45, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(5, 5, "___________", 0, 0, "L");


		
		
		$this->fpdf->Ln(4);
		$this->fpdf->SetFont('Arial', "", 8);
		$this->fpdf->Cell(75, 5, "Series of ". date('Y'), 0, 0, "L");

		$this->fpdf->Ln(9);

		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(75, 5, 'Authority to Travel is hereby granted to:', 0, 0, "L");

		$this->fpdf->Ln(8);

		$employees = explode('/', $toDetails[6]);


		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(60, 5, "NAME ", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "POSITION ", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "DIVISION/AGENCY ", 0, 0, "C");
		$this->fpdf->Ln(1);

		for ($i=0; $i < sizeof($employees); $i++) { 

		$info = $this->getrequesters_info($employees[$i]);
		$this->fpdf->Ln(6);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, $this->show_name_regular($info['firstname'] . ' ' . 
        (!empty($info['middleInitial']) ? $info['middleInitial'] . '.' : '') . ' ' . 
        $info['surname'] . ' ' . 
        $info['nameExtension']), 0, 0, "C");
		$this->fpdf->Cell(60, 5, $info['positionAbb'], 0, 0, "C");
		$this->fpdf->SetFont('Arial', "", 8);
		$this->fpdf->Cell(60, 5, $info['groupName'], 0, 0, "C");

		$this->fpdf->Ln(0.2);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "____________________________", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "____________________________", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "____________________________", 0, 0, "C");
		}

		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(60, 5, "Destination ", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "Inclusive Date/s of Travel ", 0, 0, "C");
		$this->fpdf->Cell(60, 5, "Purpose(s) of the Travel", 0, 0, "C");
		$this->fpdf->Ln(1);

		$this->fpdf->Ln(6);  // Line break
		$this->fpdf->SetFont('Arial', "", 9);
		$yPosition = $this->fpdf->GetY();
		// First column (MultiCell)
		$this->fpdf->MultiCell(60, 5, $toDetails[0], 0, "C");

		// Store the current Y position after the first MultiCell
		

		// Second column (date, using Cell to keep it in the same line)
		$this->fpdf->SetXY(73, $yPosition);  // Set Y to the same position as the first column
		$this->fpdf->MultiCell(60, 5, date("F j, Y", strtotime($toDetails[1]))." - \n".date("F j, Y", strtotime($toDetails[2])), 0, "C");

		// Third column (MultiCell for $toDetails[3])
		$this->fpdf->SetXY(135, $yPosition);  // Move further to the right (120 units in total)
		$this->fpdf->MultiCell(60, 5, $toDetails[3], 0, "C");

		// After printing all columns, set the Y position to the same Y position as the first MultiCell
		$this->fpdf->SetY($yPosition + 6);  // Keep the Y-position aligned and move down 6 units






		// FUNDING DETAILS

		$this->fpdf->Ln(12);
		$this->fpdf->SetFont('Arial', "B", 8.5);
		$this->fpdf->Cell(90, 5, "Travel Expenses to be inccured", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "B", 8);
		$this->fpdf->Cell(90, 5, "Approriate/Fund to which travel expenses would be charged to:", 0, 0, "R");
		$this->fpdf->Ln(8);
		$this->fpdf->Cell(60, 5, "", 0, 0, "L");
		
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(10, 5, "(", 0, 0, "C");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		if ($fundingdetails['funding_source'] == 'General Fund') {
			$this->fpdf->Cell(1, 5, 4, 0, 0, "R");
		}else{
			$this->fpdf->Cell(1, 5, "", 0, 0, "R");
		}


		
		
		$this->fpdf->SetFont('Arial', "B", 9);

		$this->fpdf->Cell(21, 5, " ) General Fund", 0, 0, "C");
		$this->fpdf->Cell(5, 5, "", 0, 0, "R");
		$this->fpdf->Cell(10, 5, "(", 0, 0, "R");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		if ($fundingdetails['funding_source'] == 'Project Funds') {
			$this->fpdf->Cell(4, 5, 4, 0, 0, "R");
		}else{
			$this->fpdf->Cell(1, 5, "", 0, 0, "R");
		}

		$this->fpdf->SetFont('Arial', "B", 9);

		$this->fpdf->Cell(21, 5, " ) Project Funds", 0, 0, "C");


		$this->fpdf->Cell(5, 5, "", 0, 0, "R");
		$this->fpdf->Cell(11, 5, "(", 0, 0, "R");
		$this->fpdf->SetFont('ZapfDingbats','', 12);

		if ($fundingdetails['funding_source'] == 'Others') {
			$this->fpdf->Cell(5, 5, 4, 0, 0, "R");
		}else{
			$this->fpdf->Cell(5, 5, "", 0, 0, "R");
		}

		
		$this->fpdf->SetFont('Arial', "B", 9);

		$this->fpdf->Cell(10, 5, " ) Others", 0, 0, "C");

		$this->fpdf->Ln(12);
		$this->fpdf->Cell(60, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(40, 5, $fundingdetails['project_fund_general'], 0, 0, "C");
		$this->fpdf->Cell(40, 5, $fundingdetails['project_fund_project'], 0, 0, "C");
		$this->fpdf->Cell(40, 5, $fundingdetails['project_fund_others'], 0, 0, "C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(6);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(60, 5, "Actual", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$actual_accommodation_general,"",0,"C");
		$this->fpdf->Cell(40,5,$actual_accommodation_project,"",0,"C");
		$this->fpdf->Cell(40,5,$actual_accommodation_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(6);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(60, 5, "Per Diem", 0, 0, "L");
		$this->fpdf->Cell(120, 5, "", 0, 0, "C");
		$this->fpdf->Ln(4.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "			Accommodation", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$perdiem_accommodation_general,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_accommodation_project,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_accommodation_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(4.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "			Meals/Food", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$perdiem_meals_general,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_meals_project,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_meals_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(4.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "			Incidental expenses", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$perdiem_incidental_general,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_incidental_project,"",0,"C");
		$this->fpdf->Cell(40,5,$perdiem_incidental_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");

		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(60, 5, "Transportation", 0, 0, "L");
		$this->fpdf->Cell(120, 5, "", 0, 0, "C");
		$this->fpdf->Ln(4.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "			Official Vehicle", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$transport_official_general,"",0,"C");
		$this->fpdf->Cell(40,5,$transport_official_project,"",0,"C");
		$this->fpdf->Cell(40,5,$transport_official_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(4.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "			Public Conveyance", 0, 0, "L");
		$this->fpdf->SetFont('ZapfDingbats','', 12);
		$this->fpdf->Cell(40,5,$transport_public_general,"",0,"C");
		$this->fpdf->Cell(40,5,$transport_public_project,"",0,"C");
		$this->fpdf->Cell(40,5,$transport_public_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Ln(6);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(60, 5, "Others", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(40,5,$others_details_remarks_general,"",0,"C");
		$this->fpdf->Cell(40,5,$others_details_remarks_project,"",0,"C");
		$this->fpdf->Cell(40,5,$others_details_remarks_others,"",0,"C");
		$this->fpdf->Ln(0.5);
		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Cell(60, 5, "", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");
		$this->fpdf->Cell(40, 5, "___________________", 0, 0, "C");



		$this->fpdf->Ln(8);
		$this->fpdf->SetFont('Arial', "B", 9);
		$this->fpdf->Cell(60, 5, "Remarks/Special Instructions:", 0, 0, "L");

		$this->fpdf->SetFont('Arial', "", 9);
		$this->fpdf->Ln(0);
		$this->fpdf->Cell(51, 5, "", 0, 0, "L");
		$this->fpdf->Cell(50, 5, $requestdetails['remarks'], 0, 0, "L");
		$this->fpdf->Ln(0);
		$this->fpdf->Cell(50, 5, "", 0, 0, "L");

		$this->fpdf->Cell(10, 5, "_________________________________________________________________________", 0, 0, "L");


		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "BI", 8);
		$this->fpdf->Cell(10, 5, "Note:", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "I", 8);

		$this->fpdf->MultiCell(170, 5, "A report of your travel must be submitted to the Agency Head/Supervising Official within 7 days completion of travel, liquidation of cash advance should be in accordance with Executive Order No. 298: Rules and Regulations and New Rates of Allowances for Official Local and Foreign Travels of Government Personnel.", 0, "L");

		$approver = $this->get_signatory($requestdetails['Signatory1']);

		$certifier = $this->get_signatory($requestdetails['Signatory3']);

		$this->fpdf->Ln(10);
        $this->fpdf->SetFont('Arial', "", 9);	
        $this->fpdf->Cell(110,7,'					RECOMMENDING APPROVAL:',"",0,"L");
        $this->fpdf->Cell(92,7,'APPROVED:',"",0,"L");

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
				(!empty($requestSignatories['Signatory1middleInitial']) ? $requestSignatories['Signatory1middleInitial'] . '.' : '') . ' ' . 
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
