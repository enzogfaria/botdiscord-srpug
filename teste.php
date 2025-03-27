<?php 
$jogo = 'Baldurs Gate 3';
$steam_api_url = "https://store.steampowered.com/api/storesearch/?term=".urlencode($jogo)."&cc=br&l=pt";
$itad_api_url = 'https://api.isthereanydeal.com/games/storelow/v2?key=51381c6ffa97f033b1a8fcef9a0e5ec0635d9010&country=BR&shops=61';
$id_api_url = 'https://api.isthereanydeal.com/lookup/id/title/v1';

$dataid = array (
    $jogo
);
$json_dataid = json_encode($dataid);

function getID($url, $json_dataid = null){
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_dataid);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $response = curl_exec($ch);
    
    curl_close($ch);
    return json_decode($response, true);
}

$id_data = getID($id_api_url, $json_dataid);
$id = $id_data[$jogo];

$data = array(
    $id
);
$json_data = json_encode($data);

function getApiData($url, $json_data = null){
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if(!empty($json_data)){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
    }
    $response = curl_exec($ch);
    
    curl_close($ch);
    return json_decode($response, true);
}

$steam_api_data = getApiData($steam_api_url);
$itad_api_data = getApiData($itad_api_url, $json_data);


$item = $steam_api_data['items'][0];
$nome = $item['name'];
$itemitad = $itad_api_data[0]['lows'];
$menor_preco = $itemitad[0]['price']['amount'];
$preco = isset($item['price']['final']) ? 
    'R$ '.number_format($item['price']['final']/100, 2, ',', '.') : 'Gratuito';
$menor_preco = isset($itemitad[0]['price']['amount']) ? 
'R$ '.number_format($itemitad[0]['price']['amount'], 2, ',', '.') : 'Gratuito';

print_r($menor_preco);
?>
