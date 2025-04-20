<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CertificateEmployeeCompensation_model extends CI_Model {

	public function __construct()
	{
		//parent::__construct();
		$this->load->database();
		$this->load->helper('report_helper');		
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
	
	function getSQLData($t_strEmpNmbr="",$t_strOfc="")
	{
		if($t_strEmpNmbr!='')
			$this->db->where('tblemppersonal.empNumber',$t_strEmpNmbr);
		if($t_strOfc!='')
			$this->db->where('tblempposition.group3',$t_strOfc);
		
		$this->db->select('tblemppersonal.empNumber, tblemppersonal.surname, tblemppersonal.firstname,tblemppersonal.middlename,tblemppersonal.middleInitial,tblemppersonal.nameExtension,tblemppersonal.sex,
								tblempposition.*,
								tblposition.positionDesc, tblappointment.appointmentDesc');
		$this->db->join('tblempposition',
			'tblemppersonal.empNumber = tblempposition.empNumber','left');
		$this->db->join('tblposition',
			'tblempposition.positionCode = tblposition.positionCode','left');
		$this->db->join('tblappointment',
			'tblempposition.appointmentCode = tblappointment.appointmentCode','left');
		$this->db->where('tblempposition.statusOfAppointment','In-Service');
		$this->db->order_by('tblemppersonal.surname, tblemppersonal.firstname');
		$objQuery = $this->db->get('tblemppersonal');
		return $objQuery->result_array();
	}

	function getemployeeincome($empno,$year,$month)
	{
		// $benefits = mysql_fetch_array(mysql_query("Select ' from tblincome where incomeCode='PERA' and recipient like '%".$t_arrEmpInfo['appointmentCode']."%'"));
		//$this->db->distinct();
		$this->db->Select('tblempincome.incomeCode,tblincome.incomeDesc,tblempincome.incomeAmount');
		//$this->db->where('incomeCode',$incomeCode);
		$this->db->join('tblincome','tblempincome.incomeCode = tblincome.incomeCode','inner');
		$this->db->where('empNumber',$empno);
		$this->db->where('incomeYear',$year);
		$this->db->where('incomeMonth',$month);
		//$this->db->where('appointmentCode',$appointmentCode);
		$this->db->where('tblempincome.incomeAmount>',0);
		$this->db->order_by('tblempincome.incomeMonth DESC');
		//$this->db->group_by('tblempincome.incomeCode');
		$objQuery = $this->db->get('tblempincome');
		//echo $this->db->last_query();
		$rs = $objQuery->result_array();
		if(count($rs)>0)
			return $rs;
		else
			return array();
		
	}

	function getrata($empno,$ra_ta)
	{
		// $benefitsRA = mysql_fetch_array(mysql_query("Select incRAAmount from tblempincomerata where empNumber='".$t_arrEmpInfo['empNumber']."'"));
		if($ra_ta=="RA") $select = 'incRAAmount';
		if($ra_ta=="TA") $select = 'incTAAmount';
		$this->db->Select($select);	
		$this->db->where('empNumber',$empno);
		$objQuery = $this->db->get('tblempincomerata');
		$rs = $objQuery->result_array();
		if(count($rs)>0)
			return $rs;
		else
			return array($select=>0);
	}


	function getbonus($empno,$appointmentCode,$bonusYear)
	{
		// $bonusBenefits = mysql_query("Select DISTINCT tblempincome.incomeAmount, tblincome.incomeCode, tblincome.incomeDesc
		// 										from tblempincome inner join tblincome 
		// 										on tblempincome.incomeCode = tblincome.incomeCode
		// 										where tblincome.incomeType='Bonus' and tblincome.recipient like '%".$t_arrEmpInfo['appointmentCode']."%'
		// 										AND tblempincome.incomeYear='$bonusYear'
		// 										ORDER BY tblempincome.incomeMonth ASC
		// 										");
		$this->db->Select('tblempincome.incomeAmount, tblincome.incomeCode, tblincome.incomeDesc');
		$this->db->join('tblincome',
			'tblempincome.incomeCode = tblincome.incomeCode','inner');
		$this->db->join('tblempbenefits',
			'tblempbenefits.incomeCode = tblempincome.incomeCode AND tblempbenefits.empNumber=tblempincome.empNumber','inner');
		$this->db->where('tblincome.incomeType','Bonus');
		$this->db->where('tblempincome.incomeYear',$bonusYear);
		$this->db->where('tblempbenefits.status',1);
		$this->db->where('tblempincome.empNumber',$empno);
		$this->db->order_by('tblempincome.incomeMonth ASC');
		$objQuery = $this->db->get('tblempincome');
		//echo $this->db->last_query();
		return $objQuery->result_array();
	}

	function generate($arrData)
	{		
		
		$rs=$this->getSQLData($arrData['strSelectPer']==1?$arrData['empno']:'',$arrData['strSelectPer']==2?$arrData['ofc']:'');
		
		foreach($rs as $t_arrEmpInfo):
		//while($t_arrEmpInfo=mysql_fetch_array($query)) {
			$this->fpdf->AddPage();
			//list($year,$month,$day) = split('[/,-]',$t_arrEmpInfo['firstDayAgency']);
			$arrTmpDate = explode('-',$t_arrEmpInfo['firstDayAgency']);
			$year = $arrTmpDate[0];
			$month = $arrTmpDate[1];
			$day = $arrTmpDate[2];
			/*
			$div=mysql_fetch_array(mysql_query("Select tbldivision.divisionName,tblempposition.empNumber,  
												tblempposition.divisionCode from tblempposition  
												inner join tbldivision on  
												tblempposition.divisionCode=tbldivision.divisionCode   
												where tblempposition.empNumber='".$t_arrEmpInfo['empNumber']."'"));
			*/
			
			$divisionName = office_name(employee_office($t_arrEmpInfo['empNumber']));;
			
			// if (trim($t_arrEmpInfo['nameExtension'])=="")
			// {
			// 	$name = strtoupper($t_arrEmpInfo['firstname']." ".$t_arrEmpInfo['middleInitial'].". ".$t_arrEmpInfo['surname']);
			// }									
			// else
			// {
				$name = strtoupper($t_arrEmpInfo['firstname']." ".mi($t_arrEmpInfo['middleInitial']).$t_arrEmpInfo['surname']." ".$t_arrEmpInfo['nameExtension']);
			//}
			
			// get appointment status
			// $sqlAppointment = mysql_query("SELECT appointmentDesc FROM tblappointment WHERE appointmentCode='".$t_arrEmpInfo['appointmentCode']."'");
			// $rowAppointment = mysql_fetch_array($sqlAppointment);
			$empAppointment = $t_arrEmpInfo['appointmentDesc'];
			
			// create date string
			$strMonthFull = intToMonthFull($arrData['intMonth']);
			$dateString = $strMonthFull." ".$arrData['intDay'].",  ".$arrData['intYear'];
				
			//$this->printHead();
			$this->fpdf->SetFont('Arial','B',12);
			//$this->Cell(0,0,$dateString,0,1,'R');
			$this->fpdf->Ln(15);
			$this->fpdf->SetFont('Arial','BU',14);
			$this->fpdf->Cell(0,0,"C E R T I F I C A T I O N",0,1,'C');
			$this->fpdf->Ln(20);
			$this->fpdf->SetFont('Arial','B',12);
			$this->fpdf->Cell(0,0,"TO WHOM IT MAY CONCERN:",0,0,'L');
			$this->fpdf->SetFont('Arial','',12);
			$this->fpdf->Ln(10);
			$this->fpdf->Write(5,"        This is to certify that ");
			$this->fpdf->Write(5,titleOfCourtesy($t_arrEmpInfo['sex']).' '.$name);
			$this->fpdf->Write(5," has been employed in the ");
			//$this->Write(5,AGENCY_NAME2." on ".$empAppointment." status since ");
			$this->fpdf->Write(5,getAgencyName()." since ");
			$this->fpdf->Write(5,date('d F Y',mktime(0,0,0,$month,$day,$year)));
			$this->fpdf->Write(5," and presently occupies the position of ");
			$this->fpdf->Write(5,$t_arrEmpInfo['positionDesc'].'.');
			
			$this->fpdf->Ln(10);
			
			$strPronoun = pronoun($t_arrEmpInfo['sex']);
			if($strPronoun == "Him")
			{
				$strPrn = "His";   //the next paragraph kpg gnmit mo him akward
				$strPrn2 = "he";
			}
			else
			{
				$strPrn = "Her";
				$strPrn2 = "she";
			}
			
			$this->fpdf->Write(5,"        ".$strPrn." present monthly compensation and allowances are broken down as follows:");

	/*		
			$this->Write(5,", ".$divisionName);
			$this->Write(5,", is a ".$t_arrEmpInfo['appointmentDesc']." employee with the ");
			$this->Write(5,$t_arrAgency['agencyName']." and has been in the service since ");
			$this->Write(5,date('d F Y',mktime(0,0,0,$month,$day,$year)));
			$this->SetFont('Arial','',12);
			$ctrTotal = 0;
			$this->Write(5," to date with an annual compensation and benefits as follows :");
			$this->Ln(10);
	*/
			$this->fpdf->Ln(10);
			
			$col0 = 30;
			$col1 = 70;
			$col2 = 50;
			$ctrTotal=0;
			$ctrTotalBonus=0;
			// salary	
			$this->fpdf->SetFont('Arial','U',12);
			$this->fpdf->Cell($col0,5,'', 0, 0, 'L');
			$this->fpdf->Cell($col1,5,'Remunerations', 1, 0, 'C');
			$this->fpdf->Cell($col2,5,'Amount', 1, 1, 'C');
			
			$this->fpdf->Ln(5);

			$this->fpdf->SetFont('Arial','',12);		
			$this->fpdf->Cell($col0,5,'', 0, 0, 'L');
			$this->fpdf->Cell($col1,5,'Basic Salary per month', 0, 0, 'L');
			$this->fpdf->Cell($col2,5,'P    '.number_format($t_arrEmpInfo['actualSalary'], 2,".",","), 0, 1, 'R');
			$ctrTotal=$ctrTotal+$t_arrEmpInfo['actualSalary'];
			
			// p e r a
			$benefits = $this->getemployeeincome($t_arrEmpInfo['empNumber'],$arrData['intPayrollYear'],$arrData['intPayrollMonth']);
			foreach($benefits as $row):
				// $benefits = mysql_fetch_array(mysql_query("Select incomeCode,incomeAmount from tblincome where incomeCode='PERA' and recipient like '%".$t_arrEmpInfo['appointmentCode']."%'"));
				$this->fpdf->Cell($col0,5,'', 0, 0, 'L');
				$this->fpdf->Cell($col1,5,$row['incomeDesc'], 0, 0, 'L');
				
				$this->fpdf->Cell($col2,5,number_format($row['incomeAmount'], 2,".",","), 0, 1, 'R');
				$ctrTotal=$ctrTotal+$row['incomeAmount'];
			endforeach;
			// a c a
			// $aca = mysql_fetch_array(mysql_query("Select incomeCode,incomeAmount from tblincome where incomeCode='ACA' and recipient like '%".$t_arrEmpInfo['appointmentCode']."%'"));
			// $aca = $this->getemployeeincome($t_arrEmpInfo['empNumber'],'ACA',$t_arrEmpInfo['appointmentCode']);
			// $this->fpdf->Cell($col0,5,'', 0, 0, 'L');
			// $this->fpdf->Cell($col1,5,'A C A', 0, 0, 'L');
			// $this->fpdf->Cell($col2,5,number_format($aca[0]['incomeAmount'], 2,".",","), 0, 1, 'R');
			// $ctrTotal=$ctrTotal+$aca[0]['incomeAmount'];

			// subsistence & laundry
			//$benefits3 = mysql_fetch_array(mysql_query("Select incomeCode,incomeAmount from tblincome where incomeCode='SUBSIS' and recipient like '%".$t_arrEmpInfo['appointmentCode']."%'"));
			// $subsis = $this->getemployeeincome($t_arrEmpInfo['empNumber'],'SUBSIS',$t_arrEmpInfo['appointmentCode']);
			// $this->fpdf->Cell($col0,5,'', 0, 0, 'L');
			// $this->fpdf->Cell($col1,5,'Subsistence & Laundry Allowances', 0, 0, 'L');
			//$benefits4 = mysql_fetch_array(mysql_query("Select incomeCode,incomeAmount from tblincome where incomeCode='LAUNDRY' and recipient like '%".$t_arrEmpInfo['appointmentCode']."%'"));
			// $laundry = $this->getemployeeincome($t_arrEmpInfo['empNumber'],'LAUNDRY',$t_arrEmpInfo['appointmentCode']);
			// // print_r($laundry);
			// // print_r($subsis);
			// $this->fpdf->Cell($col2,5,number_format($subsis[0]['incomeAmount'] + $laundry[0]['incomeAmount'], 2,".",","), 0, 1, 'R');
			// $ctrTotal=$ctrTotal + $subsis[0]['incomeAmount'] + $laundry[0]['incomeAmount'];
			
			// // hazard pay
			// $this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
			// $this->fpdf->Cell($col1,5,'Hazard Pay', 0, 0, 'L');
			// $this->fpdf->Cell($col2,5,number_format($t_arrEmpInfo['actualSalary'] * ($t_arrEmpInfo['hpFactor']/100), 2,".",","), 0, 1, 'R');
			// $ctrTotal=$ctrTotal + ($t_arrEmpInfo['actualSalary'] * ($t_arrEmpInfo['hpFactor']/100));
			
			// // longevity pay
			// $this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
			// $this->fpdf->Cell($col1,5,'Longevity Pay', 0, 0, 'L');
			// $this->fpdf->Cell($col2,5,number_format($t_arrEmpInfo['actualSalary'] * ($t_arrEmpInfo['longiFactor']/100), 2,".",","), 0, 1, 'R');
			// $ctrTotal=$ctrTotal + ($t_arrEmpInfo['actualSalary'] * ($t_arrEmpInfo['longiFactor']/100));
			
			// // r a t a
			// //$benefitsRA = $this->getrata($t_arrEmpInfo['empNumber'],'RA');
			// $ra = $this->getemployeeincome($t_arrEmpInfo['empNumber'],'RA',$t_arrEmpInfo['appointmentCode']);
			// //$benefitsRA = mysql_fetch_array(mysql_query("Select incRAAmount from tblempincomerata where empNumber='".$t_arrEmpInfo['empNumber']."'"));
			// //$benefitsTA = $this->getrata($t_arrEmpInfo['empNumber'],'TA');
			// $ta = $this->getemployeeincome($t_arrEmpInfo['empNumber'],'TA',$t_arrEmpInfo['appointmentCode']);
			// //$benefitsTA = mysql_fetch_array(mysql_query("Select incTAAmount from tblempincomerata where empNumber='".$t_arrEmpInfo['empNumber']."'"));
			// if($ra[0]['incomeAmount']>0 || $ta[0]['incomeAmount']>0 )
			// {
			// 	$this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
			// 	$this->fpdf->Cell($col1,5,'R A T A', 0, 0, 'L');
			// 	$this->fpdf->Cell($col2,5,number_format($ra[0]['incomeAmount'] + $ta['incomeAmount'], 2,".",","), 0, 1, 'R');
			// 	$ctrTotal=$ctrTotal + $ra['incomeAmount'] + $ra['incomeAmount'];
			// }
			//$this->Ln(5);
			$this->fpdf->SetFont('Arial','U',12);
			$this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
			$this->fpdf->Cell($col1,5,'', 0, 0, 'L');
			$this->fpdf->Cell($col2,5,'                        ', 0, 1, 'R');		
			
			// t o t a l
			$this->fpdf->SetFont('Arial','',12);
			$this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
			$this->fpdf->Cell($col1,5,'T O T A L', 0, 0, 'L');
			$this->fpdf->Cell($col2,5,'P '.number_format($ctrTotal, 2,".",","), 0, 1, 'R');
		
			$this->fpdf->Ln(10);

			$bonusYear = date('Y')-1;
			$bonusBenefits = $this->getbonus($t_arrEmpInfo['empNumber'],$t_arrEmpInfo['appointmentCode'],$bonusYear);
			// $bonusBenefits = mysql_query("Select DISTINCT tblempincome.incomeAmount, tblincome.incomeCode, tblincome.incomeDesc
			// 									from tblempincome inner join tblincome 
			// 									on tblempincome.incomeCode = tblincome.incomeCode
			// 									where tblincome.incomeType='Bonus' and tblincome.recipient like '%".$t_arrEmpInfo['appointmentCode']."%'
			// 									AND tblempincome.incomeYear='$bonusYear'
			// 									ORDER BY tblempincome.incomeMonth ASC
			// 									");
			
			if(count($bonusBenefits)>0)
			{

				$this->fpdf->Write(5,"       Further, ".$strPrn2." received the following additional remunerations other than the above during the twelve (12) month period ending December ".$bonusYear.".");		
				$this->fpdf->Ln(10);	
				//$this->Write(5,"P ");
				$ctrP = 1;
				//while($rowBenefits = mysql_fetch_array($bonusBenefits))
				foreach($bonusBenefits as $rowBenefits)
				{
					if($ctrP)
					{ $p='P'; $ctrP=0;
					}
					else $p='';	
						
					$bonusBenefit = $rowBenefits['incomeDesc'];
					$bonusAmount = $rowBenefits['incomeAmount'];
								
					$this->fpdf->Cell($col0,5,'', 0, 0, 'L');
					$this->fpdf->Cell($col1,5,$bonusBenefit, 0, 0, 'L');
					$this->fpdf->Cell($col2,5,$p." ".number_format($bonusAmount, 2,".",","), 0, 1, 'R');
					$ctrTotalBonus=$ctrTotalBonus + $bonusAmount;
				}					

				// bonus t o t a l
				$this->fpdf->Ln(5);
				$this->fpdf->SetFont('Arial','U',12);
				$this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
				$this->fpdf->Cell($col1,5,'', 0, 0, 'L');
				$this->fpdf->Cell($col2,5,'                        ', 0, 1, 'R');		

				$this->fpdf->SetFont('Arial','',12);
				$this->fpdf->Cell($col0,5,'', 0, 0, 'L');		
				$this->fpdf->Cell($col1,5,'T O T A L', 0, 0, 'L');
				$this->fpdf->Cell($col2,5,'P '.number_format($ctrTotalBonus, 2,".",","), 0, 1, 'R');

				
			}	

			$day = daySuffix($arrData['intDay']);
			$this->fpdf->Ln(10);	
			$this->fpdf->Write(5,"       Issued this ".$day." day of ".$strMonthFull." ".$arrData['intYear']." for whatever legal purpose it may serve ".strtolower(pronoun($t_arrEmpInfo['sex'])).".");		

			//$sig=explode('|',PD);
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

			$this->fpdf->SetFont('Arial','B',12);				
			$this->fpdf->Cell(80, 5, '', 0, 0, 'R');
			$this->fpdf->Cell(80, 5, $sigName, 0, 1, 'C');	

			$this->fpdf->SetFont('Arial','',12);				
			$this->fpdf->Cell(80, 5, '', 0, 0, 'R');		
			$this->fpdf->Cell(80, 5, $sigPos, 0, 1, 'C');	
			
		
		endforeach;
		echo $this->fpdf->Output();
	}
	
}
/* End of file CertificateEmployeeCompensation_model.php */
/* Location: ./application/models/reports/CertificateEmployeeCompensation_model.php */