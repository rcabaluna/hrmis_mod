<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('check_session')) {
    function check_session()
    {
        $session = isset($_SESSION['sessBoolLoggedIn']) ? 1 : 0;
        $session = $session ? $_SESSION['sessBoolLoggedIn'] : 0;

        if (!$session) :
            redirect('login');
        endif;
    }
}

# get salary schedule
if (!function_exists('emp_att_scheme')) {
    function emp_att_scheme($empNumber)
    {
        $CI = &get_instance();
        $res = $CI->db->select('tblattendancescheme.*')->join('tblattendancescheme', 'tblattendancescheme.schemeCode = tblempposition.schemeCode', 'left')->get_where('tblempposition', array('empNumber' => $empNumber))->result_array();
        return count($res) > 0 ? $res[0] : '';
    }
}

# get salary schedule
if (!function_exists('salary_schedule')) {
    function salary_schedule()
    {
        $CI = &get_instance();
        $res = $CI->db->select('salarySchedule')->get('tblagency')->result_array();
        return count($res) > 0 ? strtolower($res[0]['salarySchedule']) : '';
    }
}

if (!function_exists('employee_details')) {
    function employee_details($strEmpNo)
    {
        $CI = &get_instance();
        return $CI->db->select('tblemppersonal.*,tblposition.positionDesc,tblempposition.appointmentCode,tblempposition.actualSalary')->join('tblempposition', 'tblempposition.empNumber=tblemppersonal.empNumber')->join('tblposition', 'tblposition.positionCode=tblempposition.positionCode')->where('tblemppersonal.empNumber', $strEmpNo)->get('tblemppersonal')->result_array();
    }
}

if (!function_exists('strict_mode')) {
    function strict_mode()
    {
        $CI = &get_instance();
        //$CI->db->query("set global sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
        //$CI->db->query("set session sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
    }
}


if (!function_exists('employee_name')) {
    function employee_name($strEmpNo)
    {
        $CI = &get_instance();
        $res = $CI->db->select('surname, firstname, middlename, middleInitial, nameExtension')->get_where('tblemppersonal', array('empNumber' => $strEmpNo))->result_array();
        if (count($res) > 0) {
            $middlename = $res[0]['middlename'] == '' ? ' ' : $res[0]['middlename'];
            $mid_ini = $res[0]['middleInitial'] != '' ? str_replace('.', '', $res[0]['middleInitial']) : ($middlename != '' ? $middlename[0] : '');
            $mid_ini = $mid_ini != '' ? $mid_ini . '.' : '';
            $mid_ini = strpos($mid_ini, '.') ? $mid_ini : $mid_ini . '.';
            return $res[0]['surname'] . ', ' . $res[0]['firstname'] . ' ' . $mid_ini . ' ' . $res[0]['nameExtension'];
        } else {
            return '';
        }
    }
}

if (!function_exists('employee_name_formal')) {
    function employee_name_formal($strEmpNo)
    {
        $CI = &get_instance();
        $res = $CI->db->select('surname, firstname, middlename, middleInitial, nameExtension')->get_where('tblemppersonal', array('empNumber' => $strEmpNo))->result_array();
        if (count($res) > 0) {
            $middlename = $res[0]['middlename'] == '' ? ' ' : $res[0]['middlename'];
            $mid_ini = $res[0]['middleInitial'] != '' ? str_replace('.', '', $res[0]['middleInitial']) : ($middlename != '' ? $middlename[0] : '');
            $mid_ini = $mid_ini != '' ? $mid_ini . '.' : '';
            $mid_ini = strpos($mid_ini, '.') ? $mid_ini : $mid_ini . '.';
            return $res[0]['firstname'] . ' ' . $mid_ini . ' ' . $res[0]['surname'] . ' '  . $res[0]['nameExtension'];
        } else {
            return '';
        }
    }
}


if (!function_exists('appstatus_name')) {
    function appstatus_name($strCode)
    {
        $CI = &get_instance();
        $CI->load->model('libraries/payroll_process_model');
        $rs = $CI->payroll_process_model->getData($strCode);
        return count($rs) > 0 ? $rs[0]['appointmentDesc'] : '';
    }
}

if (!function_exists('appstatus_code')) {
    function appstatus_code($strCode)
    {
        $CI = &get_instance();
        $CI->load->model('libraries/payroll_process_model');
        $rs = $CI->payroll_process_model->getData();
        foreach ($rs as $row) :
            $arrData = explode(',', $row['processWith']);
            if (in_array($strCode, $arrData))
                return $row['appointmentCode'];
        endforeach;
    }
}

if (!function_exists('employee_office')) {
    function employee_office($strEmpNo)
    {

        $CI = &get_instance();
        //$CI->load->model('employees/employees_model');
        $rs = $CI->db->select('group1,group2,group3,group4,group5')->where('empNumber', $strEmpNo)->get('tblempposition')->result_array();
        foreach ($rs as $row) :
            if ($row['group5'] != '') return $row['group5'];
            if ($row['group4'] != '') return $row['group4'];
            if ($row['group3'] != '') return $row['group3'];
            if ($row['group2'] != '') return $row['group2'];
            if ($row['group1'] != '') return $row['group1'];
        endforeach;
    }
}


if (!function_exists('employee_office_desc')) {
    function employee_office_desc($strEmpNo)
    {

        $rsx = array();
        $CI = &get_instance();
        // $CI->load->model('employees/employees_model');
        $rs = $CI->db->select('group1,group2,group3,group4,group5')->where('empNumber', $strEmpNo)->get('tblempposition')->result_array();

        for ($i=5; $i >= 1 ; $i--) { 
            if ($rs[0]['group'.$i] != '') {
                $rsx = $CI->db->select('group'.$i.'Name')->where('group'.$i.'Code', $rs[0]['group'.$i])->get('tblgroup'.$i)->row_array();
                return $rsx['group'.$i.'Name'];
            }
        }

        
        
        // foreach ($rs as $row) :
        //     if ($row['group5'] != '') return $row['group5'];
        //     if ($row['group4'] != '') return $row['group4'];
        //     if ($row['group3'] != '') return $row['group3'];
        //     if ($row['group2'] != '') return $row['group2'];
        //     if ($row['group1'] != '') return $row['group1'];
        // endforeach;
    }
}

if (!function_exists('office_name')) {
    function office_name($code)
    {
        $CI = &get_instance();
        $arrRes = array();

        $res = $CI->db->get_where('tblgroup', array('groupcode' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['groupname'] : null);

        $res = $CI->db->get_where('tblgroup1', array('group1Code' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['group1Name'] : null);

        $res = $CI->db->get_where('tblgroup2', array('group2Code' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['group2Name'] : null);

        $res = $CI->db->get_where('tblgroup3', array('group3Code' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['group3Name'] : null);

        $res = $CI->db->get_where('tblgroup4', array('group4Code' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['group4Name'] : null);

        $res = $CI->db->get_where('tblgroup5', array('group5Code' => $code))->result_array();
        array_push($arrRes, $res != null ? $res[0]['group5Name'] : null);

        return join('', $arrRes);
    }
}

if (!function_exists('getincome_status')) {
    function getincome_status($stat)
    {
        if ($stat != '') :
            $status = array('Remove', 'On-going', 'Paused');
            return $status[$stat];
        else :
            return $stat;
        endif;
    }
}

if (!function_exists('strtofloat')) {
    function strtofloat($float)
    {
        return str_replace(',', '', $float);
    }
}

# Return Adjustment Type
if (!function_exists('adjustmentType')) {
    function adjustmentType($type = '')
    {
        $types = array(array('id' => '1', 'val' => 'Credit'), array('id' => '-1', 'val' => 'Debit'), array('id' => '2', 'val' => 'From Voucher'));
        if ($type != '') :
            $key = array_search($type, array_column($types, 'id'));
            return $types[$key]['val'];
        else :
            return $types;
        endif;
    }
}

# Return Period
if (!function_exists('periods')) {
    function periods($computation = '')
    {
        $arrPeriods = array(
            array('id' => 1, 'val' => 'Period 1'),
            array('id' => 2, 'val' => 'Period 2'),
            array('id' => 3, 'val' => 'Period 3'),
            array('id' => 4, 'val' => 'Period 4')
        );

        switch ($computation):
            case 'Monthly':
                return array($arrPeriods[0]);
                break;
            case 'Semimonthly':
            case 'Bi-Monthly':
                return array($arrPeriods[0], $arrPeriods[1]);
                break;
            case 'Weekly':
                return $arrPeriods;
                break;
            default:
                return array($arrPeriods[0], $arrPeriods[1]);
                break;
        endswitch;
    }
}

if (!function_exists('getfullname')) {
    function getfullname($fname, $lname, $mname = '', $mid = '', $ext = '')
    {
        $lname = $lname != '' ? $lname . ', ' : '';
        $mname = $mname == '' ? '' : $mname[0];
        $mid_ini = $mid != '' ? str_replace('.', '', $mid) : $mname;
        $mid_ini = $mid_ini != '' ? $mid_ini . '.' : '';
        $mid_ini = $mid_ini != '' ? strpos($mid_ini, '.') ? $mid_ini : $mid_ini . '.' : '';
        $ext = $ext != '' ? $ext . ' ' : '';
        $fullname = ucwords($lname . ' ' . $fname . ' ' . $mid_ini . ' ' . $ext);
        return $fullname;
    }
}

if (!function_exists('fix_fullname')) {
    function fix_fullname($fname, $lname, $mname = '', $mid = '', $ext = '')
    {
        $lname = $lname != '' ? $lname : '';
        $mname = $mname == '' ? '' : $mname[0];
        $mid_ini = $mid != '' ? str_replace('.', '', $mid) : $mname;
        $mid_ini = $mid_ini != '' ? $mid_ini . '. ' : '';
        $mid_ini = $mid_ini != '' ? strpos($mid_ini, '.') ? $mid_ini : $mid_ini . '.' : '';
        $ext = $ext != '' ? $ext . ' ' : '';
        $fullname = ucwords($lname . ', ' . $fname . ' ' . $mid_ini . ' ' . $ext);
        return $fullname;
    }
}


if (!function_exists('breakdates')) {
    function breakdates($from, $to)
    {
        $arrdates = array();
        while (strtotime($from) <= strtotime($to)) {
            array_push($arrdates, $from);
            $from = date("Y-m-d", strtotime("+1 day", strtotime($from)));
        }
        return $arrdates;
    }
}

if (!function_exists('breakdates')) {
    function breakdates($from, $to)
    {
        $arrdates = array();
        while (strtotime($from) <= strtotime($to)) {
            array_push($arrdates, $from);
            $from = date("Y-m-d", strtotime("+1 day", strtotime($from)));
        }
        return $arrdates;
    }
}

if (!function_exists('curryr')) {
    function curryr()
    {
        return isset($_GET['yr']) ? $_GET['yr'] : date('Y');
    }
}

if (!function_exists('currmo')) {
    function currmo()
    {
        return isset($_GET['month']) ? $_GET['month'] : date('m');
    }
}

if (!function_exists('currdfrom')) {
    function currdfrom()
    {
        return isset($_GET['datefrom']) ? $_GET['datefrom'] : date('Y-m') . '-01';
    }
}

if (!function_exists('currdto')) {
    function currdto()
    {
        return isset($_GET['dateto']) ? $_GET['dateto'] : date('Y-m') . '-' . cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
    }
}

if (!function_exists('ordinal')) {
    function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
    }
}

if (!function_exists('fixFloat')) {
    function fixFloat($number)
    {
        return floatval(str_replace(',', '', $number));
    }
}

if (!function_exists('fixArray')) {
    function fixArray($arrData)
    {
        if (gettype($arrData) == 'string') :
            $arrData = json_decode($arrData, true);
        endif;

        return $arrData;
    }
}

if (!function_exists('fixJson')) {
    function fixJson($arrJson)
    {
        if (gettype($arrJson) == 'string') :
            return $arrJson;
        else :
            return json_encode($arrJson);
        endif;
    }
}

if (!function_exists('getGroupOffice')) {
    function getGroupOffice($selected_office = '')
    {
        $CI = &get_instance();
        $CI->load->model('request_model');
        $str = '';
        $Group1 = $CI->request_model->getOfficeName(1);
        foreach ($Group1 as $g1) :
            $selected = $g1['group1Code'] == $selected_office ? 'selected' : '';
            $str .= '<option value="' . $g1['group1Code'] . '" ' . $selected . '>' . $g1['group1Name'] . '</option>';
            $Group2 = $CI->request_model->getOfficeName(2, $g1['group1Code']);
            foreach ($Group2 as $g2) :
                $selected = $g2['group2Code'] == $selected_office ? 'selected' : '';
                $str .= '<option value="' . $g2['group2Code'] . '" ' . $selected . '>&nbsp;&nbsp;' . $g2['group2Name'] . '</option>';
                $Group3 = $CI->request_model->getOfficeName(3, $g2['group2Code']);
                foreach ($Group3 as $g3) :
                    $selected = $g3['group3Code'] == $selected_office ? 'selected' : '';
                    $str .= '<option value="' . $g3['group3Code'] . '" ' . $selected . '> &nbsp;&nbsp;&nbsp;&nbsp;' . $g3['group3Name'] . '</option>';
                    $Group4 = $CI->request_model->getOfficeName(4, $g3['group3Code']);
                    foreach ($Group4 as $g4) :
                        $selected = $g4['group4Code'] == $selected_office ? 'selected' : '';
                        $str .= '<option value="' . $g4['group4Code'] . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $g4['group4Name'] . '</option>';
                        $Group5 = $CI->request_model->getOfficeName(4, $g4['group4Code']);
                        foreach ($Group5 as $g5) :
                            $selected = $g5['group5Code'] == $selected_office ? 'selected' : '';
                            $str .= '<option value="' . $g5['group5Code'] . '" ' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $g5['group5Name'] . '</option>';
                        endforeach;
                    endforeach;
                endforeach;
            endforeach;
        endforeach;
        return $str;
    }
}

if (!function_exists('plantilla_group')) {
    function plantilla_group($strCode)
    {
        $CI = &get_instance();
        $rs = $CI->db->select('plantillaGroupName')->where('plantillaGroupCode', $strCode)->get('tblplantillagroup')->result_array();
        return count($rs) > 0 ? $rs[0]['plantillaGroupName'] : '';
    }
}

if (!function_exists('position_name')) {
    function position_name($strCode)
    {
        $CI = &get_instance();
        $rs = $CI->db->select('positionDesc')->where('positionCode', $strCode)->get('tblposition')->result_array();
        return count($rs) > 0 ? $rs[0]['positionDesc'] : '';
    }
}

if (!function_exists('array_sort')) {
    function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
}

if (!function_exists('printrd')) {
    function printrd($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}

if (!function_exists('getaddress')) {
    function getaddress($lot1, $street1, $subdivision1, $barangay1, $city1, $province1)
    {
        $lot1 = $lot1 != '' ? $lot1 . ', ' : '';
        $street1 = $street1 != '' ? $street1 . ', ' : '';
        $subdivision1 = $subdivision1 != '' ? $subdivision1 . ', ' : '';
        $barangay1 = $barangay1 != '' ? $barangay1 . ', ' : '';
        $city1 = $city1 != '' ? $city1 . ', ' : '';

        $address = ucwords($lot1 . ' ' . $street1 . ' ' . $subdivision1 . ' ' . $barangay1 . ' ' . $city1 . ' ' . $province1);
        return $address;
    }
}


if (!function_exists('sendemail_new_request')) {
    function sendemail_request_to_signatory($recepient,$requesttype,$requestdate)
    {

        $CI = &get_instance();

        // Load the email library if not loaded
        $CI->load->library('email');
        
        // Email configuration
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com', // Your SMTP host
            'smtp_user' => 'rocabalunajr@region10.dost.gov.ph', // SMTP username
            'smtp_pass' => 'quqa cwlj qxhr etha', // SMTP password
            'smtp_port' => 587, // Use 465 for SSL, 587 for TLS
            'smtp_crypto' => 'tls', // Use 'ssl' for SSL
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE,
            'newline'   => "\r\n"
        );
        
        $CI->email->initialize($config);

        // Set email parameters
        $CI->email->from('hr@region10.dost.gov.ph', 'DOST 10 HR');
        $CI->email->to($recepient);
        $link = base_url('/login');
        $subject = "[HRMIS] Action Required: New " . $requesttype . " Request for Processing";
        $message = "<p>Hello,</p>
        
                    <p>A new <b>" . strtolower($requesttype) . " request</b> from the system requires your attention. Please review and take the necessary actions at your earliest convenience.</p>
                    
                    <p>You can access the request by clicking the link below:</p>
                    <p><a href='" . $link . "'>" . $link . "</a></p>
                    
                    <br>
                    <p><i><strong>Note:</strong> This is an automated message. Please do not reply to this email.</i></p>
        
                    <br>
                    <p>Best regards,<br>
                    DOST - 10 HR Unit</p>";
        

        $CI->email->subject($subject);
        $CI->email->message($message);

        // Send email
        if ($CI->email->send()) {
            return true;
        }else{
            return false;
        } 
    }
}


if (!function_exists('sendemail_update_request')) {
    function sendemail_update_request($empName, $recepient,$requesttype,$requestdate,$status)
    {
    $CI = &get_instance();

        // Load the email library if not loaded
        $CI->load->library('email');
        
        // Email configuration
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com', // Your SMTP host
            'smtp_user' => 'rocabalunajr@region10.dost.gov.ph', // SMTP username
            'smtp_pass' => 'quqa cwlj qxhr etha', // SMTP password
            'smtp_port' => 587, // Use 465 for SSL, 587 for TLS
            'smtp_crypto' => 'tls', // Use 'ssl' for SSL
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE,
            'newline'   => "\r\n"
        );
        
        $CI->email->initialize($config);

        // Set email parameters
        $CI->email->from('hr@region10.dost.gov.ph', 'DOST 10 HR');
        $CI->email->to($recepient);

        $link = base_url('/login');
    
        $subject = "[HRMIS] Notification: ".$requesttype ." Request Status";
        $message = "<p>Hello,</p>
    
                        <p>Your ".$requesttype." request, submitted on ".date('F d, Y', strtotime($requestdate)).", has been marked as <b>".strtoupper($status)."</b> by ".employee_name_formal($empName).".</p>
                        
                        <p>For more details regarding your request, please visit: <br> <a href='".$link."')>".$link."</a></p>
                        <br><br>
                        <p><i><strong>Note:</strong> This is an automated message. Please do not reply to this email.</></p>
                        
                        <br>
                        <p>Best regards,<br>
                        DOST - 10 HR Unit</p>";

        $CI->email->subject($subject);
        $CI->email->message($message);

        // Send email
        if ($CI->email->send()) {
            return true;
        }
    }

    if (!function_exists('get_email_address')) {
        function get_email_address($strEmpNo)
        {
            $CI = &get_instance();
            $rs = $CI->db->select('work_email')->where('empNumber', $strEmpNo)->get('tblemppersonal')->row_array();

            return $rs['work_email'];
        }
}


if (!function_exists('generate_travel_order_no')) {
    function generate_travel_order_no()
    {
        $CI = &get_instance();

        $count = $CI->db->where('requestCode', 'TO')->count_all_results('tblemprequest');
        
        return 'H'.date('Y') . '-' . str_pad($count+1, 4, '0', STR_PAD_LEFT);
        
    }
}

}
