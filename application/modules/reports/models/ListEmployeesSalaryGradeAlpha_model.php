<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ListEmployeesSalaryGradeAlpha_model extends CI_Model {

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

	function getSQLData($strAppStatus='')
	{
		$this->db->select('tblemppersonal.surname, tblemppersonal.firstname, tblemppersonal.nameExtension, tblemppersonal.middleInitial, tblemppersonal.middlename, tblempposition.empNumber, tblposition.positionCode, tblempposition.salaryGradeNumber, tblposition.positionCode, tblposition.positionDesc, tblempposition.statusOfAppointment, tblempposition.detailedfrom');
		
		$this->db->join('tblempposition',
			'tblemppersonal.empNumber = tblempposition.empNumber','inner');
		$this->db->join('tblposition',
			'tblempposition.positionCode = tblposition.positionCode','inner');
		//$this->db->join('tblsalarysched','tblempposition.salaryGradeNumber = tblsalarysched.salaryGradeNumber','inner');
		$this->db->where('tblempposition.statusOfAppointment','In-Service');
		//$this->db->where('tblsalarysched.salaryGradeNumber',$strSGNumber);
		if($strAppStatus!='')
			$this->db->where('tblempposition.appointmentCode',$strAppStatus);
		
		$this->db->order_by('tblemppersonal.surname ASC, tblemppersonal.firstname ASC, tblemppersonal.middlename ASC');
		$objQuery = $this->db->get('tblemppersonal');
		//echo $this->db->last_query();
		return $objQuery->result_array();
	}

	function generate($arrData)
	{		
		$this->fpdf->AddPage('P','A4');
		$this->fpdf->Ln(18);
		$this->fpdf->SetFont('Arial','B',10);
		$this->fpdf->Cell(0,6,strtoupper('list of employees by salary grade (alphabetical)'), 0, 0, 'C');
		$this->fpdf->Ln(5);
		$this->fpdf->Cell(0,6,"As of " . date("Y"), 0, 0, 'C');
		$this->fpdf->Ln(15);
		
		$this->fpdf->SetFont('Arial','B',10);
		$this->fpdf->SetFillColor(150,150,150);
		$this->fpdf->Cell(70,6,"EMPLOYEE NAME",1,0,'C',1);
		$this->fpdf->Cell(85,6,"OFFICE - POSITION",1,0,'C',1);
		$this->fpdf->Cell(30,6,"SALARY GRADE",1,0,'C',1);
		$this->fpdf->Ln(6);
		
		$objQuery = $this->getSQLData($arrData['strAppStatus']);
		$this->fpdf->SetFillColor(255,255,255);
		$this->fpdf->SetDrawColor(0,0,0);
		$this->fpdf->SetFont('Arial','',10);
		$i=1;
		foreach($objQuery as $arrEmp)
		//while($arrSalaryGrade = mysql_fetch_array($objSalaryGrade))
		{
			
			$extension = (trim($arrEmp['nameExtension'])=="") ? "" : " ".$arrEmp['nameExtension'];		
			//$name = $arrEmp['firstname']." ".$arrEmp['middleInitial'].". ".$arrEmp['surname']." ".$extension;
			$name=$arrEmp["surname"].', '.$arrEmp["firstname"].$extension.' '.mi($arrEmp["middlename"]);

	/*			
			$this->Cell(100,0,$name,0,0,L);
			$this->Cell(220,0,$arrEmp["positionDesc"],0,0,L);
			$this->Cell(0,0,$arrEmp["salaryGradeNumber"],0,0,R);
			$this->Ln(5);
	*/		
					//$office = new General();
					$strOffice = office_name(employee_office($arrEmp['empNumber']));
					$strOfficePosition = $strOffice.' - '.$arrEmp["positionDesc"];

			$w = array(70,85,30);
			$Ln = array('L','C','C');
			$this->fpdf->SetWidths($w);
			$this->fpdf->SetAligns($Ln);
			$this->fpdf->FancyRow(array($i.'. '.$name,$strOfficePosition,$arrEmp["salaryGradeNumber"]),array(1,1,1),$Ln);
			$i++;
		}

		/* Signatory */
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
/* End of file ListEmployeesSalaryGradeAlpha_model.php */
/* Location: ./application/modules/reports/models/reports/ListEmployeesSalaryGradeAlpha_model.php */