<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/TAD/vendor/autoload.php';
require APPPATH.'libraries/TAD/tad/lib/TADFactory.php';
require APPPATH.'libraries/TAD/tad/lib/TAD.php';
require APPPATH.'libraries/TAD/tad/lib/TADResponse.php';
require APPPATH.'libraries/TAD/tad/lib/Providers/TADSoap.php';
require APPPATH.'libraries/TAD/tad/lib/Providers/TADZKLib.php';
require APPPATH.'libraries/TAD/tad/lib/Exceptions/ConnectionError.php';
require APPPATH.'libraries/TAD/tad/lib/Exceptions/FilterArgumentError.php';
require APPPATH.'libraries/TAD/tad/lib/Exceptions/UnrecognizedArgument.php';
require APPPATH.'libraries/TAD/tad/lib/Exceptions/UnrecognizedCommand.php';

use TADPHP\TADFactory;

class Tad_Lib {

    private $tad; 
    
    public function __construct(){
        $this->tad = (new TADFactory(array('ip' => '192.168.128.5')))->get_instance();

    }

    public function get_DTRs($current_date,$biometricsid){

        $data = [];
        $groupedData = [];
        $data = $this->tad->get_att_log(['pin'=>$biometricsid])->filter_by_date(['start' => $current_date ,'end' => $current_date])->to_array();
        
            if ($data) {
                // echo $biometricsid;
                // echo "<br>";
                // echo $current_date;
                // echo "<br>";
                // echo date('Y-m-d H:i:s');
                // echo "<br>";
                // IF MULTIPLE DTR ENTRIES
                    if (isset($data['Row'][0])) {
                        foreach ($data['Row'] as $row) {

                            $dateTime = $row['DateTime'];
                            $date = date('Y-m-d', strtotime($dateTime));
                        
                            $filteredItem = [
                                'PIN' => $row['PIN'],
                                'DateTime' => $row['DateTime'],
                            ];
            
                            if (!isset($groupedData[$date])) {
                                $groupedData[$date] = [];
                            }
                            
                            $groupedData[$date][] = $filteredItem;
                        }
                    }elseif (isset($data['Row']['PIN'])) {
                        $dateTime = $data['Row']['DateTime'];
                            $date = date('Y-m-d', strtotime($dateTime));
                            $filteredItem = [
                                'PIN' => $data['Row']['PIN'],
                                'DateTime' => $data['Row']['DateTime'],
                            ];
                            
                            if (!isset($groupedData[$date])) {
                                $groupedData[$date] = [];
                            }
                            
                            $groupedData[$date][] = $filteredItem;
                    }
                    return $groupedData;
            }else{
                return $groupedData;
            }
            
    }
    
    public function get_Users(){
        $users = $this->tad->get_device_name();
        
        return $users;
    }

    function isNestedArray($array) {
        foreach ($array as $value) {
            if (is_array($value)) {
                return true;
            }
        }
        return false;
    }

}