<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ListEmployeesEducationalAttainment_model extends CI_Model {

	var $w=array(70,70,60,60);

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('report_helper');
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

	function getSQLData($empno="",$strAppStatus="",$intMonth="")
	{
		
		//$strAppStatus = $_GET['cboAppointmentStatus'];
		$this->db->select('tblemppersonal.empNumber, tblempschool.levelCode,
											tblempschool.course,tbleducationallevel.level');
		
		$this->db->join('tblempschool',
			'tblemppersonal.empNumber = tblempschool.empNumber','left');
		$this->db->join('tbleducationallevel',
			'tblempschool.levelCode = tbleducationallevel.levelCode','left');
		$this->db->join('tblempposition',
			'tblemppersonal.empNumber = tblempposition.empNumber','left');
		if($empno!='')
			$this->db->where('tblempschool.empNumber',$empno);
		if($strAppStatus!='')
			$this->db->where('tblempposition.appointmentCode',$strAppStatus);

		$this->db->order_by('tbleducationallevel.level asc');
		$objQuery = $this->db->get('tblemppersonal');
		//echo $this->db->last_query();
		return $objQuery->result_array();
	}

	function get_degrees()
	{
		$this->db->Select('DISTINCT(courseCode),courseDesc');
		$this->db->where('courseDesc!=','');
		$this->db->order_by('courseDesc');
		$objQuery = $this->db->get('tblcourse');
		return $objQuery->result_array();
	}

	function get_courses($strDegree)
	{
		// $empQuery=mysql_query("SELECT  DISTINCT tblempschool.empNumber, tblempschool.courseCode, tblcourse.courseDesc,  tblemppersonal.surname,tblemppersonal.firstname,tblemppersonal.middlename, 
		// 	tblemppersonal.nameExtension,tblempposition.detailedfrom,tblposition.positionDesc     
		// 				FROM ( tblempschool 
		// 				LEFT  JOIN tblemppersonal ON tblempschool.empNumber = tblemppersonal.empNumber ) 
		// 				LEFT JOIN tblempposition ON tblempposition.empNumber=tblemppersonal.empNumber 
		// 				LEFT JOIN tblposition ON tblempposition.positionCode = tblposition.positionCode	
		// 				LEFT  JOIN tblcourse ON tblcourse.courseCode = tblempschool.courseCode 
		// 				WHERE tblempschool.courseCode = '".$degree["courseCode"]."' AND tblempschool.graduated ='Y'
		// 				AND (tblempschool.levelCode ='Ph.D.' || tblempschool.levelCode ='MA/MS' || tblempschool.levelCode ='CLG' || tblempschool.levelCode ='TRT')
		// 				AND tblempposition.statusOfAppointment='In-Service' AND (tblempposition.detailedfrom = '0' OR tblempposition.detailedfrom = '2') 
		// 				ORDER BY tblemppersonal.surname ASC, tblemppersonal.firstname ASC");
		$this->db->Select('tblempschool.empNumber, tblempschool.courseCode, tblcourse.courseDesc,  tblemppersonal.surname,tblemppersonal.firstname,tblemppersonal.middlename, 
			tblemppersonal.nameExtension,tblempposition.detailedfrom,tblposition.positionDesc');
		$this->db->join('tblemppersonal','tblempschool.empNumber = tblemppersonal.empNumber','left');
		$this->db->join('tblempposition','tblempposition.empNumber=tblemppersonal.empNumber','left');
		$this->db->join('tblposition','tblempposition.positionCode = tblposition.positionCode','left');
		$this->db->join('tblcourse','tblcourse.courseCode = tblempschool.courseCode','left');
		$this->db->where('tblempschool.courseCode',$strDegree)->where('tblempschool.graduated','Y')->where("(tblempschool.levelCode ='Ph.D.' || tblempschool.levelCode ='MA/MS' || tblempschool.levelCode ='CLG' || tblempschool.levelCode ='TRT')")->where('tblempposition.statusOfAppointment','In-Service');
		//$this->db->where('','');
		$this->db->order_by('tblemppersonal.surname ASC, tblemppersonal.firstname ASC');
		$objQuery = $this->db->get('tblempschool');
		return $objQuery->result_array();
	}

	function generate($arrData)
	{		
		$this->fpdf->AddPage('P','A4');
		$this->fpdf->SetFont('Arial','B',10);
		//$this->Cell(0,6,'Republic of the Philippines', 0, 0, 'C');
		//$this->Ln(5);
		//$this->Cell(0,6,$this->agencyName, 0, 0, 'C');
		$this->fpdf->Ln(5);
		//$this->Cell(0,6,$this->agencyAdd, 0, 0, 'C');		
		$this->fpdf->Ln(20);
		//$head = new title();
		$this->fpdf->SetFont('Arial','B',12);
		$this->fpdf->Cell(0,6,strtoupper('list of employees by educational attainment'), 0, 0, 'C');
		$this->fpdf->Ln(5);
		$this->fpdf->Cell(0,6,"As of " . date("Y"), 0, 0, 'C');
		$this->fpdf->Ln(15);	
		//$degQuery=mysql_query("SELECT DISTINCT courseCode,courseDesc from tblcourse where courseDesc!='' order by courseDesc");
		$degQuery = $this->get_degrees();
		foreach($degQuery as $degree)
		//while ($degree=mysql_fetch_array($degQuery))
		{
			// $empQuery=mysql_query("SELECT  DISTINCT tblempschool.empNumber, tblempschool.courseCode, tblcourse.courseDesc,  tblemppersonal.surname,tblemppersonal.firstname,tblemppersonal.middlename, 
			// tblemppersonal.nameExtension,tblempposition.detailedfrom,tblposition.positionDesc     
			// 			FROM ( tblempschool 
			// 			LEFT  JOIN tblemppersonal ON tblempschool.empNumber = tblemppersonal.empNumber ) 
			// 			LEFT JOIN tblempposition ON tblempposition.empNumber=tblemppersonal.empNumber 
			// 			LEFT JOIN tblposition ON tblempposition.positionCode = tblposition.positionCode	
			// 			LEFT  JOIN tblcourse ON tblcourse.courseCode = tblempschool.courseCode 
			// 			WHERE tblempschool.courseCode = '".$degree["courseCode"]."' AND tblempschool.graduated ='Y'
			// 			AND (tblempschool.levelCode ='Ph.D.' || tblempschool.levelCode ='MA/MS' || tblempschool.levelCode ='CLG' || tblempschool.levelCode ='TRT')
			// 			AND tblempposition.statusOfAppointment='In-Service' AND (tblempposition.detailedfrom = '0' OR tblempposition.detailedfrom = '2') 
			// 			ORDER BY tblemppersonal.surname ASC, tblemppersonal.firstname ASC");
			$empQuery = $this->get_courses($degree["courseCode"]);
			if (count($empQuery)>0 AND strlen($degree["courseDesc"])!=0){
				$this->fpdf->SetFont('Arial','B',12);
				$this->fpdf->Cell(0,0,$degree["courseDesc"]);
				$this->fpdf->Ln(4);

				$this->fpdf->SetFont('Arial','B',10);
				$this->fpdf->SetFillColor(200,200,200);
				$this->fpdf->Cell(75,5,"NAME",1,0,'C',1);
				$this->fpdf->Cell(115,5,"OFFICE - POSITION",1,0,'C',1);
				$this->fpdf->Ln(5);
			}
			foreach($empQuery as $emp)
			{
			//while($emp = mysql_fetch_array($empQuery)){

				$this->fpdf->SetFont('Arial','',10);
				
				$name=$emp["surname"].', '.$emp["firstname"].' '.$emp["nameExtension"].' '.mi($emp["middlename"]);
				$name = utf8_decode($name);
				/*
				$this->Cell(20,0,$name);
				$this->Ln(5);
				*/
				
				$strOffice = office_name(employee_office($emp['empNumber']));
				$strOfficePosition = $strOffice.' - '.$emp['positionDesc'];

				$w = array(75,115);
				$Ln = array('L','C');
				$this->fpdf->SetWidths($w);
				$this->fpdf->SetAligns($Ln);
				$this->fpdf->FancyRow(array($name,$strOfficePosition),array(1,1));
				$this->fpdf->Ln();

			}//end inner while
			//$this->fpdf->Ln();
		}//end outer while
		
		 if($this->fpdf->GetY()>195)
			 $this->fpdf->AddPage();
			 
		
			
		$this->fpdf->Ln(20);
		$this->fpdf->Cell(30);
		$this->fpdf->Cell(30);				
		$this->fpdf->SetFont('Arial','B',12);		
		$this->fpdf->Cell(0,10,"Certified Correct:",0,0,'L');
		

		$sig=getSignatories($arrData['intSignatory']);
		if(count($sig)>0)
		{
			$sigName = $sig[0]['signatory'];
			$sigPos = $sig[0]['signatoryPosition'];
		}
		else
		{
			$sigName='';
			$sigPos='';
		}
		$this->fpdf->Ln(20);
		$this->fpdf->Cell(30);
		$this->fpdf->Cell(30);				
		$this->fpdf->SetFont('Arial','B',12);		
		$this->fpdf->Cell(0,10,$sigName,0,0,'L');

		$this->fpdf->Ln(4);
		$this->fpdf->Cell(30);
		$this->fpdf->Cell(30);				
		$this->fpdf->SetFont('Arial','',12);				
		$this->fpdf->Cell(0,10,$sigPos,0,0,'L');
		
		$this->fpdf->Ln(4);
		$this->fpdf->Cell(30);
		$this->fpdf->Cell(30);				
		$this->fpdf->SetFont('Arial','',12);				
		//$this->fpdf->Cell(0,10,$sig[0],0,0,'L');
		$this->fpdf->Ln(15);
			
		
		
		echo $this->fpdf->Output();
	}
	
}
/* End of file ListEmployeesEducationalAttainment_model.php */
/* Location: ./application/modules/reports/models/reports/ListEmployeesEducationalAttainment_model.php */