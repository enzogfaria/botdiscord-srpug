<?php 


class Teste3 {
    private static ?Teste3 $instance = null;

    private function __construct() {} // Impede instância externa

    public static function getInstance(): Teste3 {
        if (self::$instance === null) {
            self::$instance = new Teste3();
        }
        return self::$instance;
    }

    public function getApiData($url, $json_data = null, $json_dataid = null){
        if($json_dataid !== null){
            $ch = curl_init($url);
    
            // curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_dataid);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            echo "CAIU AQUIIIIIIIIIIIIIII";
        }else {
            $ch = curl_init($url);
            // curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (!empty($json_data)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));
                echo "AQUI AGORAAAAAAAA";
            }
    
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

}

?>