<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ReportLeave_rpt_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->helper('report_helper');	

		$this->db->initialize();	
	}

    public function getleave_types(){
        return $this->db->get('tblleave')->result_array();
    }

    public function getLatestBalance($empNumber)
	{
		$this->db->where("empNumber",$empNumber);
		$this->db->order_by('lb_id DESC');
		$res = $this->db->get('tblempleavebalance')->result_array();
		
        if (count($res) > 1) {
            return $res[1];
        }else{
            return $res[0];
        }
	}

    public function getleave_details($requestid){

        $this->db->select('req.*, pers.surname, pers.firstname, pers.middleInitial, pers.middlename, pers.nameExtension, pos.positionDesc, emppos.actualSalary');
        $this->db->from('tblemprequest AS req');
        $this->db->join('tblemppersonal AS pers', 'pers.empNumber = req.empNumber');
        $this->db->join('tblempposition AS emppos', 'emppos.empNumber = req.empNumber');
        $this->db->join('tblposition AS pos', 'pos.positionCode = emppos.positionCode');
        $this->db->where('req.requestID', $requestid);

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

    public function get_request_signatories($requestid){
        $this->db->select("
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
        p3.nameExtension AS Signatory3Extension
    ");
    $this->db->from('tblrequestflow r');
    $this->db->join('tblemppersonal p1', "p1.empNumber = SUBSTRING_INDEX(SUBSTRING_INDEX(r.Signatory1, ';', -1), ';', 1)", 'left');
    $this->db->join('tblemppersonal p2', "p2.empNumber = SUBSTRING_INDEX(SUBSTRING_INDEX(r.Signatory2, ';', -1), ';', 1)", 'left');
    $this->db->join('tblemppersonal p3', "p3.empNumber = SUBSTRING_INDEX(SUBSTRING_INDEX(r.Signatory3, ';', -1), ';', 1)", 'left');
    $this->db->where('r.reqID', $requestid);
    
    $query = $this->db->get();
    return $query->row_array();
    }

    public function generate($requestid){

        $leaveDetails = $this->getleave_details($requestid);

        $requestSignatories = $this->get_request_signatories($leaveDetails['requestflowid']);

        $requestDetails = explode(';', $leaveDetails['requestDetails']);

        $office = employee_office_desc($leaveDetails['empNumber']);

        $this->fpdf->SetTitle('Leave Application Form');
		$this->fpdf->SetLeftMargin(18);
		$this->fpdf->SetRightMargin(15);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on",10);
		$this->fpdf->AddPage('P','Legal','');

        $this->fpdf->SetFont('Arial', "I", 8);
        $this->fpdf->Cell(0, 4, "Civil Service Form No. 6", 0, 0, "L");		
		$this->fpdf->Cell(0, 4, "", 0, 0, "R");
		$this->fpdf->Ln(3);
		$this->fpdf->Cell(0, 4, "Revised 2020", 0, 0, "L");		
		$this->fpdf->Cell(0, 4, "", 0, 0, "R");
	
		$this->fpdf->Ln(3);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(0, 5, "Republic of the Philippines", 0, 0, "C");
        $this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(0, 5, "DEPARTMENT OF SCIENCE AND TECHNOLOGY - X", 0, 0, "C");
        $this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "B", 10);
		$this->fpdf->Cell(0, 5, "Cagayan de Oro City", 0, 0, "C");

        $this->fpdf->Ln(12);
		$this->fpdf->SetFont('Arial', "B", 18);
		$this->fpdf->Cell(0, 5, "APPLICATION FOR LEAVE ", 0, 0, "C");

        $this->fpdf->Ln(10);
        $this->fpdf->SetFont('Arial', "", 10);	
		$this->fpdf->Cell(70,6,'1. OFFICE/DEPARTMENT',"LT",0,"L");
		$this->fpdf->Cell(25,6,'2. NAME',"T",0,"L");
        $this->fpdf->Cell(25,6,'(Last)',"T",0,"L");
        $this->fpdf->Cell(30,6,'(First)',"T",0,"L");
        $this->fpdf->Cell(30,6,'(Middle)',"RT",0,"L");


        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 8);

		$this->fpdf->Cell(70,6,strtoupper("  ".$office),"L",0,"L");
		$this->fpdf->Cell(20,6,'',"",0,"L");
        $this->fpdf->Cell(20,6,strtoupper($leaveDetails['surname']),"",0,"C");
        $this->fpdf->Cell(30,6,strtoupper($leaveDetails['firstname'].' '.$leaveDetails['nameExtension']),"",0,"C");
        $this->fpdf->Cell(40,6,strtoupper($leaveDetails['middlename']),"R",0,"C");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
		$this->fpdf->Cell(30,6,'3. DATE OF FILING   ',"LT",0,"L");
        $this->fpdf->SetFont('Arial', "BU", 8.5);	
		$this->fpdf->Cell(23,6,strtoupper(date('M d, Y',strtotime($leaveDetails['requestDate']))),"T",0,"L");

        $this->fpdf->SetFont('Arial', "", 8.5);	
		$this->fpdf->Cell(20,6,'4. POSITION',"T",0,"L");

        $this->fpdf->SetFont('Arial', "BU", 8);	
        $this->fpdf->Cell(62,6,strtoupper($leaveDetails['positionDesc']),"T",0,"C");
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(15,6,'5. SALARY',"T",0,"L");

        $this->fpdf->SetFont('Arial', "BU", 8.5);	
        $this->fpdf->Cell(30,6,"P ".number_format($leaveDetails['actualSalary'],2,'.',','),"TR",0,"C");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 12);	
        $this->fpdf->Cell(180,6,'',"T",0,"L");
        $this->fpdf->Ln(0.5);
        $this->fpdf->Cell(180,6,'6. DETAILS OF APPLICATION',"RLT",0,"C");
        
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 12);	
        $this->fpdf->Cell(180,6,'',"T",0,"L");

        $this->fpdf->Ln(0.5);
        $this->fpdf->SetFont('Arial', "", 8.5);	

        $this->fpdf->Cell(98,6,'6.A TYPE OF TO BE AVAILED OF',"RLT",0,"L");
        $this->fpdf->Cell(82,6,'6.B DETAILS OF LEAVE',"RLT",0,"L");

        
        foreach ($this->getleave_types() AS $leave_types) {
            
            if($leave_types['leaveCode'] == $requestDetails[0]){
                $check = "4";
            }else{
                $check = "";
            }

            $this->fpdf->Ln(6);
            $this->fpdf->Rect(20, $this->fpdf->GetY() + 1.5, 3, 3);
            $this->fpdf->SetFont('Arial', "B", 6.5);	
            $this->fpdf->Cell(1,6,'',"L",0,"L");

            $this->fpdf->SetFont('ZapfDingbats','', 12);
            $this->fpdf->Cell(0.1,6,$check,"",0,"L");
            $this->fpdf->SetFont('Arial', "", 7);	
            $this->fpdf->Cell(96.9,6,"        ".$leave_types['leaveType']." ". $leave_types['description'],"R",0,"L");
            
            $this->fpdf->SetFont('Arial', "", 8.5);	

            $vl_w = $vl_a = $vl_w_remarks = $vl_a_remarks = $sl_i = $sl_o = $sl_i_remarks = $sl_o_remarks = $check_slw_remarks = '';

            
            if($requestDetails[8] == 'within the country'){
                $vl_w = "4";
                $vl_w_remarks = $requestDetails[6];
            }elseif ($requestDetails[8] == 'abroad') {
                    $vl_a = "4";
                    $vl_a_remarks = $requestDetails[6];
            }

            if($requestDetails[7] == 'in patient'){
                $sl_i = "4";
                $sl_i_remarks = $requestDetails[6];
            }elseif ($requestDetails[7] == 'out patient') {
                    $sl_o = "4";
                    $sl_o_remarks = $requestDetails[6];
            }

            if ($requestDetails[0] == "SLW") {
                $check_slw_remarks = $requestDetails[6];

            }

            switch($leave_types['leave_id']){
                case 1:
                    $this->fpdf->SetFont('Arial', "I", 8.5);
                    $this->fpdf->Cell(82,6,'   In case of Vacation/Special Previlege Leave: ',"LR",0,"L");
                    break;
                case 2:
                    
                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->Cell(3,6,'',"L",0,"L");
                    $this->fpdf->SetFont('ZapfDingbats','', 12);

                    $this->fpdf->Cell(0.1,6,$vl_w,"",0,"L");
                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(38  ,6,'        Within the Philippines ',"",0,"L");
                    $this->fpdf->SetFont('Arial', "BU", 8.5);	
                    $this->fpdf->Cell(40.9  ,6,$vl_w_remarks,"R",0,"L");

                    break;
                case 3:
                    $this->fpdf->Cell(3,6,'',"L",0,"L");
                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->SetFont('ZapfDingbats','', 12);
                    $this->fpdf->Cell(0.1,6,$vl_a,"",0,"L");    

                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(35,6,'        Abroad (Specify)',"",0,"L");
                    $this->fpdf->SetFont('Arial', "BU", 8.5);	
                    $this->fpdf->Cell(43.9  ,6,$vl_a_remarks,"R",0,"L");
                    break;
                case 4:
                $this->fpdf->Cell(82,6,'   In case of Sick Leave:',"LR",0,"L");
                    break;
                case 5:
                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->Cell(3,6,'',"",0,"L");
                    $this->fpdf->SetFont('ZapfDingbats','', 12);
                    $this->fpdf->Cell(0.1,6,$sl_i,"",0,"L");

                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(28,6,'        In Hospital (Specify Illness)',"",0,"L");
                    $this->fpdf->Cell(50.9  ,6,$sl_i_remarks,"R",0,"C");
                    break;
                case 6:
                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->Cell(3,6,'',"",0,"L");
                    $this->fpdf->SetFont('ZapfDingbats','', 12);
                    $this->fpdf->Cell(0.1,6,$sl_o,"",0,"L");

                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(28,6,'        Out Patient (Specify Illness)',"",0,"L");
                    $this->fpdf->Cell(50.9  ,6,$sl_o_remarks,"R",0,"C");
                    break;
                case 7:
                    $this->fpdf->Cell(82,6,'    ___________________________________',"LR",0,"L");
                    break;
                case 8:
                    $this->fpdf->SetFont('Arial', "I", 8.5);
                    $this->fpdf->Cell(82,6,'    In case of Special Leave Benefits for Women:',"LR",0,"L");
                    break;
                case 9:
                    
                    $this->fpdf->Cell(82,6,'    (Specify Illness) '.$check_slw_remarks,"LR",0,"L");
                    break;
                case 10:
                    $this->fpdf->Cell(82,6,'    _______________________________________',"LR",0,"L");
                    break;
                case 11:
                    $this->fpdf->SetFont('Arial', "I", 8.5);
                    $this->fpdf->Cell(82,6,'   In case of Study Leave:',"LR",0,"L");
                    break;
                case 12:
                    $sl_masters = '';
                    if ($requestDetails[9] == "completion of master's degree") {
                        $sl_masters = "4";
                    }

                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->Cell(3,6,'',"L",0,"L");
                    $this->fpdf->SetFont('ZapfDingbats','', 12);

                    $this->fpdf->Cell(0.1,6,$sl_masters,"",0,"L");
                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(78.9,6,"        Completion of Master's Degree","R",0,"L");
                    break;
                case 13:
                    $sl_masters = '';
                    if ($requestDetails[9] == "bar/board examination review") {
                        $sl_masters = "4";
                    }

                    $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
                    $this->fpdf->Cell(3,6,'',"L",0,"L");
                    $this->fpdf->SetFont('ZapfDingbats','', 12);

                    $this->fpdf->Cell(0.1,6,$sl_masters,"",0,"L");
                    $this->fpdf->SetFont('Arial', "", 8.5);	
                    $this->fpdf->Cell(78.9,6,'        BAR/Board Examination Review',"R",0,"L");
                    break;

            }
            
        }
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "I", 8.5);
        $this->fpdf->Cell(98,6,'',"LR",0,"L");

        $this->fpdf->Cell(82,6,'   Other Purpose:',"LR",0,"L");
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);
        $this->fpdf->Cell(98,6,'        Others:',"LR",0,"L");
        $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
        $this->fpdf->Cell(82,6,'        Monetization of Leave Credits',"LR",0,"L");
        $this->fpdf->Ln(6);
        $this->fpdf->Cell(98,6,'    ___________________________________',"LR",0,"L");
        $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
        $this->fpdf->Cell(82,6,'        Terminal Leave',"LR",0,"L");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(98,6,'6C. NUMBER OF WORKING DAYS APPLIED FOR',"RLT",0,"L");
        $this->fpdf->Cell(82,6,'6.D COMMUTATION',"RLT",0,"L");

        // NO OF DAYS
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 8.5);	
        $this->fpdf->Cell(98, 6,"              ".$requestDetails[3] . ' ' . ($requestDetails[3] > 1 ? 'days' : 'day'), "RL", 0, "L");

        $this->fpdf->Ln(0);
        $this->fpdf->Cell(98, 6,"       __________________________________________", "RL", 0, "L");

        $check_commutation_r = $check_commutation_nr = '';
        if ($requestDetails[13] == "requested") {
            $check_commutation_r = 4;
        }

        if ($requestDetails[13] == "not-requested") {
            $check_commutation_nr = 4;
        }

        // COMMUTATION NOT REQUESTED
        $this->fpdf->Cell(3,6,'',"L",0,"L");
        $this->fpdf->SetFont('ZapfDingbats','', 12);
        $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
        $this->fpdf->Cell(0.1,6,$check_commutation_nr,"",0,"L");
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(78.9,6,'     Not Requested',"R",0,"L");
        $this->fpdf->Ln(6);

        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(98,6,'          INCLUSIVE DATES',"RL",0,"L");
        
        // COMMUTATION REQUESTED
        $this->fpdf->Cell(3,6,'',"L",0,"L");
        $this->fpdf->SetFont('ZapfDingbats','', 12);
        $this->fpdf->Rect(120, $this->fpdf->GetY() + 1, 3, 3);
        $this->fpdf->Cell(0.1,6,$check_commutation_r,"",0,"L");
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(78.9,6,'         Requested',"R",0,"L");
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 8.5);	
        $this->fpdf->Cell(98,6,"              ".strtoupper(date("F d, Y",strtotime($requestDetails[1]))." - ".date("F d, Y",strtotime($requestDetails[2]))),"RL",0,"L");
        $this->fpdf->Ln(0);
        $this->fpdf->Cell(98, 6,"       __________________________________________", "RL", 0, "L");
        if (!empty($leaveDetails['empNumber'])) {
            $image = "uploads/employees/esignature/" . $leaveDetails['empNumber'] . ".png";
        
            if (file_exists($image)) {
                $this->fpdf->SetFont('Arial', "I", 8);
                $this->fpdf->Cell(30, 6, '', "L", 0, "C");
                $this->fpdf->Cell(40, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 30), 0, 0, 'C', false);
                $this->fpdf->Cell(12, 6, '', "R", 0, "C");
            } else {
                // If the image file doesn't exist, display an empty cell
                $this->fpdf->Cell(82, 6, '', "RL", 0, "C");
            }
        } else {
            $this->fpdf->Cell(82, 6, '', "RL", 0, "C");
        }
        
        $this->fpdf->Ln(6);
        $this->fpdf->Cell(98,6,'',"RL",0,"C");
        $this->fpdf->SetFont('Arial', "BU", 8.5);	
        $applicant_sig = $this->get_signatory($leaveDetails['empNumber']);
        $this->fpdf->Cell(82,6,strtoupper($applicant_sig['firstname'] . ' ' . 
        (!empty($applicant_sig['middleInitial']) ? $applicant_sig['middleInitial'] . '.' : '') . ' ' . 
        $applicant_sig['surname'] . ' ' . 
        $applicant_sig['nameExtension'])
        ,"RL",0,"C");
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(98,6,'',"RL",0,"L");
        $this->fpdf->Cell(82,6,'(Signature of Applicant)',"RL",0,"C");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "B", 12);	
        $this->fpdf->Cell(180,6,'',"T",0,"L");
        $this->fpdf->Ln(0.5);
        $this->fpdf->Cell(180,6,'7. DETAILS OF ACTION ON APPLICATION',"RLT",0,"C");
        
        $this->fpdf->Ln(6);
        $this->fpdf->Cell(180,6,'',"T",0,"L");

        $this->fpdf->Ln(0.5);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(98,6,'7A. CERTIFICATION OF LEAVE CREDITS',"RLT",0,"L");
        $this->fpdf->Cell(82,6,'7B. RECOMMENDATION',"RLT",0,"L");


        $latestBalance = $this->getLatestBalance($leaveDetails['empNumber']);


        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(40,6,'AS of',"L",0,"R");
        $this->fpdf->SetFont('Arial', "BU", 9);	
        $this->fpdf->Cell(58,6,strtoupper(date('F Y',strtotime( $latestBalance['periodYear'].'-'.$latestBalance['periodMonth'].'-01'))),"R",0,"L");

        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(82,6,'    For approval',"RL",0,"L");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(7,6,'',"L",0,"C");
        $this->fpdf->Cell(32,6,'',"RLT",0,"C");
        $this->fpdf->Cell(25,6,'Vacation Leave',"RLT",0,"C");
        $this->fpdf->Cell(26,6,'Sick Leave',"RLT",0,"C");
        $this->fpdf->Cell(8,6,'',"",0,"C");
        $this->fpdf->SetFont('Arial', "", 8.5);	

        if (!empty($leaveDetails['Signatory2']) && empty($leaveDetails['Signatory3']) && $leaveDetails['requestStatus'] == "DISAPPROVED") {
        $this->fpdf->Cell(35,6,'    For disapproval due to   ',"L",0,"L");
        $this->fpdf->SetFont('Arial', "BU", 8);	
        $this->fpdf->Cell(47,6,$leaveDetails['remarks'],"R",0,"L");
        }else{
            $this->fpdf->Cell(82,6,'    For disapproval due to _________________________',"RL",0,"L");

        }





        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "I", 8);	
        $this->fpdf->Cell(7,6,'',"L",0,"C");
        $this->fpdf->Cell(32,6,'Total Earned',"RLT",0,"C");
        $this->fpdf->Cell(25,6,$latestBalance['vlBalance'],"RLT",0,"C");
        $this->fpdf->Cell(26,6,$latestBalance['slBalance'],"RLT",0,"C");
        $this->fpdf->Cell(8,6,'',"",0,"C");
        $this->fpdf->Cell(82,6,'            __________________________________________',"RL",0,"L");
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "I", 8);	
        $this->fpdf->Cell(7,6,'',"L",0,"C");
        $this->fpdf->Cell(32,6,'Less this application',"RLTB",0,"C");

        $vldays = $sldays = '';

        if ($requestDetails[0] == 'VL' || $requestDetails[0] == 'FL') {
            $vldays = $requestDetails[3];
        }else{
            $vldays = 0.00;
        }

        if ($requestDetails[0] == 'SL') {
            $sldays = $requestDetails[3];
        }else{
            $sldays = 0.00;
        }


        $this->fpdf->Cell(25,6,number_format($vldays,3,'.',''),"RLTB",0,"C");
        $this->fpdf->Cell(26,6,number_format($sldays,3,'.',''),"RLTB",0,"C");
        $this->fpdf->Cell(8,6,'',"",0,"C");
        $this->fpdf->Cell(82,6,'            __________________________________________',"RL",0,"L");

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "I", 8);	
        $this->fpdf->Cell(7,6,'',"L",0,"C");
        $this->fpdf->Cell(32,6,'Remaining',"RLTB",0,"C");

        $vldays = $sldays = '';

        if ($requestDetails[0] == 'VL' || $requestDetails[0] == 'FL') {
            $vldays = $requestDetails[3];
        }else{
            $vldays = 0.00;
        }

        if ($requestDetails[0] == 'SL') {
            $sldays = $requestDetails[3];
        }else{
            $sldays = 0.00;
        }


        $this->fpdf->Cell(25,6,number_format($latestBalance['vlBalance']-$vldays, 3, '.', ''),"RLTB",0,"C");
        $this->fpdf->Cell(26,6,number_format($latestBalance['slBalance']-$sldays, 3, '.', ''),"RLTB",0,"C");
        $this->fpdf->Cell(8,6,'',"",0,"C");
        $this->fpdf->Cell(82,6,'',"RL",0,"L");

        $this->fpdf->Ln(6);
        $approver1 = $this->get_signatory($leaveDetails['Signatory1']);
        if ($approver1) {
            $image = "uploads/employees/esignature/" . $leaveDetails['Signatory1'] . ".png";
            $this->fpdf->SetFont('Arial', "I", 8);    
            $this->fpdf->Cell(30,6,'',"L",0,"C");
            
            // Check if the image file exists before adding it
            if (file_exists($image)) {
                $this->fpdf->Cell(28, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 50), 0, 0, 'C', false);
            } else {
                $this->fpdf->Cell(28, 6, 'No Signature', 0, 0, 'C'); // Placeholder text
            }
        
            $this->fpdf->Cell(40,6,$leaveDetails['Sig1DateTime'],0,0,"L");
        } else {
            $this->fpdf->Cell(98,6,'',"RL",0,"C");
        }
        
        $approver2 = $this->get_signatory($leaveDetails['Signatory2']);
        if ($approver2) {
            $image = "uploads/employees/esignature/" . $leaveDetails['Signatory2'] . ".png";
            $this->fpdf->SetFont('Arial', "I", 8);    
            $this->fpdf->Cell(25,6,'',"L",0,"C");
        
            // Check if the image file exists before adding it
            if (file_exists($image)) {
                $this->fpdf->Cell(25, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 50), 0, 0, 'C', false);
            } else {
                $this->fpdf->Cell(25, 6, 'No Signature', 0, 0, 'C'); // Placeholder text
            }
        
            $this->fpdf->Cell(32,6,$leaveDetails['Sig2DateTime'],"R",0,"L");
        } else {
            $this->fpdf->Cell(82,6,'',"RL",0,"C");
        }
        

        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "BU", 8.5);	
        if (!$approver1) {
            $this->fpdf->Cell(98,6,strtoupper($requestSignatories['Signatory1firstname'] . ' ' . 
            (!empty($requestSignatories['Signatory1middleInitial']) ? $requestSignatories['Signatory1middleInitial'] . '.' : '') . ' ' . 
            $requestSignatories['Signatory1surname'] . ' ' . 
            $requestSignatories['Signatory1Extension'])
            ,"RL",0,"C");
        }
        else{

            $this->fpdf->Cell(98,6,strtoupper($approver1['firstname'] . ' ' . 
            (!empty($approver1['middleInitial']) ? $approver1['middleInitial'] . '.' : '') . ' ' . 
            $approver1['surname'] . ' ' . 
            $approver1['nameExtension'])
            ,"RL",0,"C");
        }
        
        if (!$approver2) {
            $this->fpdf->Cell(82,6,strtoupper($requestSignatories['Signatory2firstname'] . ' ' . 
            (!empty($requestSignatories['Signatory2middleInitial']) ? $requestSignatories['Signatory2middleInitial'] . '.' : '') . ' ' . 
            $requestSignatories['Signatory2surname'] . ' ' . 
            $requestSignatories['Signatory2Extension'])
            ,"RL",0,"C");
        }
        else{
            $this->fpdf->Cell(82,6,strtoupper($approver2['firstname'] . ' ' . 
            (!empty($approver2['middleInitial']) ? $approver2['middleInitial'] . '.' : '') . ' ' . 
            $approver2['surname'] . ' ' . 
            $approver2['nameExtension'])
            ,"RL",0,"C");
        }
        $this->fpdf->Ln(6);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(98,7,'(Authorized Officer)',"RLB",0,"C");
        $this->fpdf->Cell(82,7,'(Authorized Officer)',"RLB",0,"C");

        $this->fpdf->Ln(7.5);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(90,6,'7C APPROVED FOR',"LT",0,"L");
        $this->fpdf->Cell(90,6,'7D. DISAPPROVED DUE TO:',"RT",0,"L");

        $this->fpdf->Ln(6);
        if (isset($requestDetails['14']) && $requestDetails['14'] != 0) {
            $this->fpdf->SetFont('Arial', "BU", 8.5);	
            $this->fpdf->Cell(10, 6, '   ' . $requestDetails['14'].'   ', "L", 0, "C");
            $this->fpdf->SetFont('Arial', "", 8.5);	
            $this->fpdf->Cell(80, 6,'days with pay', "", 0, "L");
        } else {
            $this->fpdf->Cell(90, 6, '  ____ days with pay', "L", 0, "L");
        }

        if (!empty($leaveDetails['Signatory3']) && $leaveDetails['requestStatus'] == "DISAPPROVED") {
            $this->fpdf->Cell(8,6,"        ",0,0,"L");
            $this->fpdf->SetFont('Arial', "BU", 8);	
            $this->fpdf->Cell(82,6,$leaveDetails['remarks'],"R",0,"L");
            }else{
                $this->fpdf->Cell(90,6,'___________________________________________',"R",0,"C");
            }

            
        $this->fpdf->Ln(5);
        if (isset($requestDetails['15']) && $requestDetails['15'] != 0) {
            $this->fpdf->SetFont('Arial', "BU", 8.5);	
            $this->fpdf->Cell(10, 6, '   ' . $requestDetails['15'].'   ', "L", 0, "C");
            $this->fpdf->SetFont('Arial', "", 8.5);	
            $this->fpdf->Cell(80, 6,'days without pay', "", 0, "L");
        } else {
            $this->fpdf->Cell(90, 6, ' ____   days without pay', "L", 0, "L");
        }
        $this->fpdf->Cell(90,5,'___________________________________________',"R",0,"C");

        $this->fpdf->Ln(5);
        if (isset($requestDetails['16']) && $requestDetails['16'] != 0) {
            $this->fpdf->SetFont('Arial', "BU", 8.5);	
            $this->fpdf->Cell(10, 6, '   ' . $requestDetails['16'].'   ', "L", 0, "C");
            $this->fpdf->SetFont('Arial', "", 8.5);	
            $this->fpdf->Cell(80, 6,'Others (specify)', "", 0, "L");
        } else {
            $this->fpdf->Cell(90, 6, ' ____   Others (specify)', "L", 0, "L");
        }
        
        $this->fpdf->Cell(90,5,'___________________________________________',"R",0,"C");
        
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(180,5,'',"RL",0,"C");
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(180,5,'',"RL",0,"C");

        $this->fpdf->Ln(5);
        $approver3 = $this->get_signatory($leaveDetails['Signatory3']);
        if ($approver3) {
            $image = "uploads/employees/esignature/" . $leaveDetails['Signatory3'] . ".png";
            $this->fpdf->SetFont('Arial', "I", 8);    
            $this->fpdf->Cell(75,6,'',"L",0,"C");

            // Check if the image file exists before adding it
            if (file_exists($image)) {
                $this->fpdf->Cell(20, 6, $this->fpdf->Image($image, $this->fpdf->GetX(), $this->fpdf->GetY(), 30), 0, 0, 'C', false);
            } else {
                $this->fpdf->Cell(20, 6, 'No Signature', 0, 0, 'C'); // Placeholder text
            }

            $this->fpdf->Cell(85,6,$leaveDetails['Sig3DateTime'],"R",0,"L");
        } else {
            $this->fpdf->Cell(180,6,'',"RL",0,"C");
        }


        $this->fpdf->Ln(5);
        $this->fpdf->Cell(180,5,'',"RL",0,"C");
        $this->fpdf->SetFont('Arial', "BU", 9);	
        $this->fpdf->Ln(5);
        if (!$approver3) {
            $this->fpdf->Cell(180,6,strtoupper($requestSignatories['Signatory3firstname'] . ' ' . 
            (!empty($requestSignatories['Signatory3middleInitial']) ? $requestSignatories['Signatory3middleInitial'] . '.' : '') . ' ' . 
            $requestSignatories['Signatory3surname'] . ' ' . 
            $requestSignatories['Signatory3Extension'])
            ,"RL",0,"C");
        }else{
            $this->fpdf->Cell(180,6,strtoupper($approver3['firstname'] . ' ' . 
            (!empty($approver3['middleInitial']) ? $approver3['middleInitial'] . '.' : '') . ' ' . 
            $approver3['surname'] . ' ' . 
            $approver3['nameExtension'])
            ,"RL",0,"C");
        }
        
        $this->fpdf->Ln(5);
        $this->fpdf->SetFont('Arial', "", 8.5);	
        $this->fpdf->Cell(180,5,'(Authorized Official)',"RLB",0,"C");

        $this->fpdf->Output();
    }

}