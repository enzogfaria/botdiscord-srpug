<?php 
require_once 'Teste3.php';
$jogo = 'Red Dead Redemption 2';
$steam_api_url = "https://store.steampowered.com/api/storesearch/?term=".urlencode($jogo)."&cc=br&l=pt";
$itad_api_url = 'https://api.isthereanydeal.com/games/storelow/v2?key=51381c6ffa97f033b1a8fcef9a0e5ec0635d9010&country=BR&shops=61';
$id_api_url = 'https://api.isthereanydeal.com/lookup/id/title/v1';

$t3 = Teste3::getInstance();

$dataid = array (
    $jogo
);
$json_dataid = json_encode($dataid);

$id_data = $t3->getApiData($id_api_url, $json_dataid);
$id = $id_data[$jogo];

$data = array(
    $id
);
$json_data = json_encode($data);

$itad_api_data = $t3->getApiData($itad_api_url, $json_data);

$steam_api_data = $t3->getApiData($steam_api_url);


$item = $steam_api_data['items'][0];
$nome = $item['name'];
$itemitad = $itad_api_data[0]['lows'];
$menor_preco = $itemitad[0]['price']['amount'];
$preco = isset($item['price']['final']) ? 
    'R$ '.number_format($item['price']['final']/100, 2, ',', '.') : 'Gratuito';
$menor_preco = isset($itemitad[0]['price']['amount']) ? 
'R$ '.number_format($itemitad[0]['price']['amount'], 2, ',', '.') : 'Gratuito';

print_r($id);
?>
