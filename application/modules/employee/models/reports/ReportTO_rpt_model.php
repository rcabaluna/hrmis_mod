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

	function generate($arrData)
	{
		$empno = $arrData['strEmpNo'] == "" ? $_SESSION['sessEmpNo'] : $arrData['strEmpNo'];
		$emp = $this->Hr_model->getData($empno, '', 'all')[0];

		$this->fpdf->SetTitle('Travel Order');
		$this->fpdf->SetLeftMargin(10);
		$this->fpdf->SetRightMargin(10);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on", 10);
		$this->fpdf->AddPage('P', '', 'A4');


		$this->fpdf->SetFont('Arial', "", 12);
		$this->fpdf->Cell(0, 5, "", 0, 1, "C");
		$this->fpdf->Cell(0, 5, "Department of Science and Technology - X ", 0, 1, "C");	//Republic of the Philippines
		$this->fpdf->SetFont('Arial', "B", 12);
		$this->fpdf->Cell(0, 5, '', 0, 1, "C");
		$this->fpdf->SetFont('Arial', "", 12);
		$this->fpdf->Cell(0, 5, '', 0, 0, "C");
		$this->fpdf->Ln(5);

		$this->fpdf->SetFont('Arial', "B", 12);
		$this->fpdf->Cell(0, 5, "TRAVEL ORDER", 0, 0, "C");

		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(30, 5, "Name: " . employee_name($empno), 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, '', 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		// $this->fpdf->Cell(15, 5, "Salary:", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");

		$this->fpdf->Ln(7);

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(30, 5, "Position: " . $emp['positionDesc'], 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(15, 5, "Division: " . office_name($emp['group2']), 0, 0, "L"); //Station
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");

		$this->fpdf->Ln(7);

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(30, 5, "Departure Date: " . date("F j, Y", strtotime($arrData['dtmTOdatefrom'])), 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(15, 5, "Time:", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		// $this->fpdf->Cell(75, 5, "                       ", 0, 0, "L");

		$this->fpdf->Ln(7);

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(30, 5, "Return Date: " . date("F j, Y", strtotime($arrData['dtmTOdateto'])), 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		$this->fpdf->Cell(75, 5, "", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(15, 5, "Time:", 0, 0, "L");
		$this->fpdf->SetFont('Arial', "BU", 10);
		// $this->fpdf->Cell(75, 5, "                       ", 0, 0, "L");

		$this->fpdf->Ln(10);

		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(90, 5, "Destination (Place/Office)", 1, 0, "C");

		$this->fpdf->Cell(90, 5, "Purpose", 1, 0, "C");
		$this->fpdf->Ln(5);
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(90, 5, $arrData['strDestination'], 1, 0, "L");
		$this->fpdf->SetFont('Arial', "", 10);
		$this->fpdf->Cell(90, 5, $arrData['strPurpose'], 1, 0, "L");


		$this->fpdf->Ln(10);
		$this->fpdf->Cell(270, 5, "Date : " . date("F j, Y"), 0, 0, "C");
		$this->fpdf->Ln(10);
		$this->fpdf->SetFont('Arial', "BI", 8);
		$this->fpdf->Cell(200, 5, "", 0, 0, "C");
		$this->fpdf->Ln(15);
		$this->fpdf->SetFont('Arial', "", 8);
		$this->fpdf->Cell(0, 4, "**************** NO SIGNATURE NEEDED. THIS DOCUMENT HAS BEEN APPROVED ONLINE ****************", 0, 1, "C");
		// $arrGet = $this->input->get();

		// $strPrgrph1 = "In reply to your letter of  ";
		// $strPrgrph2 = "accepted";
		// $strPrgrph3 = " to take effect ";
		// $strPrgrph4 = " at the close of the office hours on ";        
		// $this->fpdf->Ln(15);
		// $this->fpdf->SetFont('Arial', "", 12);
		// $this->fpdf->Write(5,$strPrgrph1);
		// $this->fpdf->SetFont('Arial', "B", 12);
		// $this->fpdf->Write(5,$strPrgrph2);
		// $this->fpdf->SetFont('Arial', "", 12);
		// $this->fpdf->Write(5,$strPrgrph3);
		// $this->fpdf->SetFont('Arial', "B", 12);
		// $this->fpdf->Write(5,$strPrgrph4);
		// $this->fpdf->SetFont('Arial', "", 12);

		// $this->fpdf->Ln(20);
		// $this->fpdf->Cell(0,10,"Very truly yours,",0,0,'L');

		echo $this->fpdf->Output();
	}
}
/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */
