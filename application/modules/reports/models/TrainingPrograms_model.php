<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class TrainingPrograms_model extends CI_Model {

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
		
		$this->fpdf->SetLeftMargin(20);
		$this->fpdf->SetRightMargin(20);
		$this->fpdf->SetTopMargin(10);
		$this->fpdf->SetAutoPageBreak("on",10);
		
		$this->fpdf->SetFont('Arial','B',12);
		$this->fpdf->Ln(10);

		$arrGet = $this->input->get();
		
		//$t_dtmRcvDate = $arrData['dtReceivedDay']." ".date('F',strtotime(date('Y').'-'.$arrData['dtReceivedMonth'].'-'.date('d')))." ".$arrData['dtReceivedYear'];
		//$t_dtmLtrDate = $arrData['dtLetterDay']." ".date('F',strtotime(date('Y').'-'.$arrData['dtLetterMonth'].'-'.date('d')))." ".$arrData['dtLetterYear'];
		//$t_dtmAcptDate = $arrData['dtAcceptedDay']." ".date('F',strtotime(date('Y').'-'.$arrData['dtAcceptedMonth'].'-'.date('d')))." ".$arrData['dtAcceptedYear'];

		$rs = $this->getSQLData($arrData['strSelectPer']==1?$arrData['empno']:'',$arrData['strSelectPer']==2?$arrData['ofc']:'');
		foreach($rs as $t_arrEmpInfo):
			$this->fpdf->AddPage('P','','A4');
			$extension = (trim($t_arrEmpInfo['nameExtension'])=="") ? "" : " ".$t_arrEmpInfo['nameExtension'];		
			$strName = $t_arrEmpInfo['firstname']." ".mi($t_arrEmpInfo['middleInitial']).$t_arrEmpInfo['surname'].$extension;
			
			$this->fpdf->Ln(30);
			$this->fpdf->SetFont('Arial', "B", 14);
			$this->fpdf->Cell(0, 5, "LIST OF TRAINING PROGRAMS", 0, 0, "C");
			$this->fpdf->SetFont('Arial', "", 12);
			$this->fpdf->Cell(0, 5, $strName, 0, 1, "L");
			$this->fpdf->Cell(0, 5, $t_arrEmpInfo['positionDesc'], 0, 0, "L");
			
			$this->fpdf->Cell(0, 5, trim(office_name(employee_office($t_arrEmpInfo['empNumber']))), 0, 0, "L");
			$this->fpdf->Ln(15);
			
			$this->fpdf->SetFont('Arial', "", 10);
			
					$this->fpdf->Ln(5);
					$this->fpdf->SetFont('Arial','B',8);
					$this->fpdf->SetFillColor(180,180,180);
					$this->fpdf->SetFillColor(180,180,180);					
					$this->fpdf->SetWidths(array(185));
					$this->fpdf->SetAligns(array('C'));
			
					$this->fpdf->SetFillColor(210,210,210);
					$this->fpdf->Cell(55,5,"Employee",1,0,'C',1);
					$this->fpdf->Cell(30,5,"Training Duration",1,0,'C',1);
					$this->fpdf->Cell(20,5,"# of Hours",1,0,'C',1);
					$this->fpdf->Cell(40,5,"Venue",1,0,'C',1);
					$this->fpdf->Cell(35,5,"Conducted By",1,0,'C',1);
					$this->fpdf->Ln(5);	

					// $empQuery = mysql_query("Select tblemppersonal.firstname,tblemppersonal.surname,tblemppersonal.middleInitial,tblemppersonal.nameExtension from tblemppersonal INNER JOIN 
					// tblempposition ON tblemppersonal.empNumber = tblempposition.empNumber WHERE
					// tblemppersonal.empNumber='".$rs[$i]['empNumber']."'
					//  AND  tblempposition.statusOfAppointment = 'In-Service' AND ( tblempposition.detailedfrom='2' OR  tblempposition.detailedfrom='0') 
					// ");
					//$empArr = mysql_fetch_array($empQuery);
					// $extension = (trim($empArr['nameExtension'])=='') ? "" : " ".$empArr['nameExtension'] ;
					// $name = $empArr['firstname']." ".$empArr['middleInitial'].". ".$empArr['surname'].$extension;
					
					$this->fpdf->SetWidths(array(55,30,20,40,40));
					$this->fpdf->SetAligns(array('L','C','C','L','L'));
					// $this->fpdf->Row(array($name,$rs[$i]['trainingStartDate'].' - '.$rs[$i]['trainingEndDate'],$rs[$i]['trainingHours'],$rs[$i]['trainingVenue'],$rs[$i]['trainingConductedBy']), 1);
					$this->fpdf->Row(array());
			
		endforeach;
		 
		echo $this->fpdf->Output();
	}
	
	function getSQLData($t_strEmpNmbr="",$t_strOfc="")
	{
	
		if($t_strEmpNmbr!='')
			$this->db->where('tblemppersonal.empNumber',$t_strEmpNmbr);
		if($t_strOfc!='')
			$this->db->where('tblempposition.group3',$t_strOfc);
		$this->db->select('tblemppersonal.empNumber, tblemppersonal.surname, 
			tblemppersonal.firstname, tblemppersonal.middlename,tblemppersonal.middleInitial,tblemppersonal.nameExtension, tblemppersonal.sex, 
			tblposition.positionDesc, 
			tblemppersonal.comTaxNumber, tblemppersonal.issuedAt, 
			tblemppersonal.issuedOn, tblempposition.positionDate,tblappointment.appointmentDesc,
			tblempposition.firstDayAgency');
		$this->db->join('tblempposition',
			'tblemppersonal.empNumber = tblempposition.empNumber','left');
		$this->db->join('tblposition',
			'tblempposition.positionCode = tblposition.positionCode','left');
		$this->db->join('tblappointment',
			'tblappointment.appointmentCode=tblempposition.appointmentCode ','left');
		$this->db->order_by('tblemppersonal.surname, tblemppersonal.firstname');
		$objQuery = $this->db->get('tblemppersonal');
		return $objQuery->result_array();
	
	}

	function getCriteriaData($t_strEmpNmbr="")
	{
		$criteria = strtr($_GET['criteria'],' ','%');
		$sql = "Select tblemptraining.* from tblemptraining 
										 LEFT JOIN tblempposition ON tblemptraining.empNumber = tblempposition.empNumber WHERE tblempposition.statusOfAppointment = 'In-Service' AND ( tblempposition.detailedfrom='2' OR  tblempposition.detailedfrom='0')
										AND tblemptraining.trainingDesc like '%".$criteria."%' 
										  Order by tblemptraining.trainingDesc";	
		$cn= new MySQLHandler2;
		$cn->init();
		$rs=$cn->Select($sql);
		return $rs;		
	}
	
}
/* End of file Reminder_renewal_model.php */
/* Location: ./application/models/reports/Reminder_renewal_model.php */