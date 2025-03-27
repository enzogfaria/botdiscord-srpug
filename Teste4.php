<?php 


class Teste4 {
    private static ?Teste4 $instance = null;

    private function __construct() {} // Impede instância externa

    public static function getInstance(): Teste4 {
        if (self::$instance === null) {
            self::$instance = new Teste4();
        }
        return self::$instance;
    }

    function getID($url, $json_dataid = null){
        $ch2 = curl_init($url);
    
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $json_dataid);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $response = curl_exec($ch2);
        
        curl_close($ch2);
        return json_decode($response, true);
    }

}

?>