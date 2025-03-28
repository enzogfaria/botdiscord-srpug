<?php 
include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Interactions\Command\Command;
use Discord\WebSockets\Intents;
use Dotenv\Dotenv;
require_once 'APIdata.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$discord = new Discord([
    'token' => $_ENV['DISCORD_TOKEN'],
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
]);

$discord->on('ready', function (Discord $discord) {
    echo "PUG SOLTO!", PHP_EOL;

    $discord->listenCommand('preço', function (Interaction $interaction) {
        // adiciona a mensagem de espera do bot
        $interaction->acknowledgeWithResponse()->then(function () use ($interaction) {

            $getapi = APIdata::getInstance();

            // nome do jogo a partir dos dados da interação
            $jogo = $interaction->data->options['jogo']->value;
        
            $jogos_alias = [
                'gta v' => 'Grand Theft Auto V',
                // outros jogos com alias aqui
            ];
            
            $jogo = $jogos_alias[strtolower($jogo)] ?? $jogo;
        
            // pegar api e dados da steam e isthereanydeal
            $steam_api_url = "https://store.steampowered.com/api/storesearch/?term=".urlencode($jogo)."&cc=br&l=pt";
            $itad_api_url = "https://api.isthereanydeal.com/games/storelow/v2?key=" . $_ENV['API_KEY'] . "&country=BR&shops=61";
            $id_api_url = "https://api.isthereanydeal.com/lookup/id/title/v1?key=" . $_ENV['API_KEY'];

            // armazena o nome do jogo e pega o id
            $dataid = array (
                $jogo
            );
            $json_dataid = json_encode($dataid);

            $id_data = $getapi->getApiData($id_api_url, null, $json_dataid);
            $id = $id_data[$jogo];

            // armazena o nome do jogo
            $data = array(
                $id
            );
            $json_data = json_encode($data);
            
            // armazena os dados do jogo
            $itad_api_data = $getapi->getApiData($itad_api_url, $json_data, null);
            $steam_api_data = $getapi->getApiData($steam_api_url);
            
            if($itad_api_data === false || $id_data == false) {
                $interaction->updateOriginalResponse(MessageBuilder::new()->setContent("❌ Não foi possível recuperar os dados da API IsThereAnyDeal."));
                return;
            }
            if($steam_api_data == false){
                $interaction->updateOriginalResponse(MessageBuilder::new()->setContent("❌ Não foi possível recuperar os dados da API Steam."));
            }

            // exibe a mensagem da resposta
            if (!empty($steam_api_data['items'])) {
                $item = $steam_api_data['items'][0];
                $nome = $item['name'];
                $itemitad = $itad_api_data[0]['lows'];
                $menor_preco = $itemitad[0]['price']['amount'];
                $preco = isset($item['price']['final']) ? 'R$ '.number_format($item['price']['final']/100, 2, ',', '.') : 'Gratuito';
                if(empty($itemitad)){
                    $menor_preco = "**❌ ERRO!** Certifique-se de digitar o nome corretamente.";
                }else {
                    $menor_preco = isset($itemitad[0]['price']['amount']) ? 'R$ '.number_format($itemitad[0]['price']['amount'], 2, ',', '.') : 'Gratuito';
                }
                
                $link = "https://store.steampowered.com/app/{$item['id']}";
        
                $interaction->updateOriginalResponse(
                    MessageBuilder::new()->setContent(
                        "🎮 **$nome** na Steam! 🔗 [Clique aqui]($link)\n" .
                        "💵 Valor: {$preco}\n" . 
                        "📉 Menor preço histórico: {$menor_preco}\n"
                    )
                );
            } else {
                $interaction->updateOriginalResponse(
                    MessageBuilder::new()->setContent("❌ Não encontrei esse jogo na Steam. 😕")
                );
            }
        });
    });      
});

$discord->run();