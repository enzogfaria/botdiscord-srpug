<?php 
include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Interactions\Command\Command;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$discord = new Discord([
    'token' => $_ENV['DISCORD_TOKEN'],
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot ligado!", PHP_EOL;

    $discord->listenCommand('preço', function (Interaction $interaction) {
        //nome do jogo
        $jogo = $interaction->data->options['jogo']->value;
    
        //GTA V de frescura 
        if (stripos($jogo, 'gta v') !== false) {
            $jogo = 'Grand Theft Auto V';
        }
    
        // pegar api e dados da steam e isthereanydeal
        $steam_api_url = "https://store.steampowered.com/api/storesearch/?term=".urlencode($jogo)."&cc=br&l=pt";
        $itad_api_url = 'https://api.isthereanydeal.com/games/storelow/v2?key=51381c6ffa97f033b1a8fcef9a0e5ec0635d9010&country=BR&shops=61';

        $data = array(
            '018d937f-1ae9-734c-ba47-bd357cf07edd'
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
    
        if (!empty($steam_api_data['items'])) {
            $item = $steam_api_data['items'][0];
            $nome = $item['name'];
            $itemitad = $itad_api_data[0]['lows'];
            $menor_preco = $itemitad[0]['price']['amount'];
            $preco = isset($item['price']['final']) ? 
                'R$ '.number_format($item['price']['final']/100, 2, ',', '.') : 'Gratuito';
            $menor_preco = isset($itemitad[0]['price']['amount']) ? 
            'R$ '.number_format($itemitad[0]['price']['amount'], 2, ',', '.') : 'Gratuito';
            $link = "https://store.steampowered.com/app/{$item['id']}";
    
            $response = MessageBuilder::new()->setContent(
                "🎮 **$nome** na Steam! 🔗 [Clique aqui]($link)\n" .
                "💵 Valor: {$preco}\n" . 
                "📉 Menor preço histórico: {$menor_preco}\n"
            );
        } else {
            $response = MessageBuilder::new()->setContent("Não encontrei esse jogo na Steam. 😕");
        }

        $interaction->respondWithMessage($response);
    });
});

$discord->run();