<?php 
namespace App;
class APIdata {
    private static ?APIdata $instance = null;

    private function __construct() {}

    public static function getInstance(): APIdata {
        if (self::$instance === null) {
            self::$instance = new APIdata();
        }
        return self::$instance;
    }

    public function getApiData($url, $json_data = null, $json_dataid = null){
        if($json_dataid !== null){
            $ch = curl_init($url);
    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_dataid);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        }else {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty($json_data)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));
            }
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

}

?>