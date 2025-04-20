<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Chart_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}

	public function plantilla_positions()
	{
		return $this->db->select('tblplantilla.plantillaID,tblplantilla.itemNumber,tblposition.positiondesc,tblempposition.empNumber')
		->join('tblposition','tblplantilla.positioncode=tblposition.positionCode','left')
		->join('tblempposition','tblempposition.itemNumber=tblplantilla.itemNumber','left')
		->where('tblplantilla.itemNumber!=','')
		->group_by('tblplantilla.itemNumber,tblplantilla.plantillaID,tblposition.positiondesc,tblempposition.empNumber')
		->get('tblplantilla');
	}

	public function gender_appointment($strAppointment,$strGender)
	{
		$objQuery = $this->db->select('COUNT(sex) AS total')
				->join('tblempposition','tblemppersonal.empNumber=tblempposition.empNumber','left')
				->where('tblempposition.statusOfAppointment','In-Service')
				->where('tblempposition.appointmentCode',$strAppointment)
				->where('tblemppersonal.sex',$strGender)
				->get('tblemppersonal');
		//echo $this->db->last_query();
		return $objQuery->result_array();
	}

	// function getGenderByAppointment($strAppointment)
	// {
	// 	$sql="SELECT sex,appointmentCode FROM tblemppersonal 
	// 			LEFT JOIN tblempposition ON tblemppersonal.empNumber=tblempposition.empNumber
	// 			WHERE tblempposition.statusOfAppointment='In-Service'
	// 			AND tblempposition.appointmentCode='".$strAppointment."'
	// 			";
	// 	//echo $sql."<br>";		
	// 	$results = mysql_query($sql);
	// 	$intMale=0;$intFemale=0;
	// 	while($row=mysql_fetch_array($results))
	// 	{
	// 		if($row['sex']=="M")
	// 			$intMale+=1;
	// 		else
	// 			$intFemale+=1;	
	// 	}
	// 	return array($intMale,$intFemale);
	// }
}