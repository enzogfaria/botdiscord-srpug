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
    
        // pegar api da steam
        $api_url = "https://store.steampowered.com/api/storesearch/?term=".urlencode($jogo)."&cc=br&l=pt";
        $dados = @json_decode(file_get_contents($api_url), true);
    
        if (!empty($dados['items'])) {
            $item = $dados['items'][0];
            $nome = $item['name'];
            $preco = isset($item['price']['final']) ? 
                'R$ '.number_format($item['price']['final']/100, 2, ',', '.') : 'Gratuito';
            $link = "https://store.steampowered.com/app/{$item['id']}";
    
            $response = MessageBuilder::new()->setContent(
                "🎮 **$nome** na Steam! 🔗 [Clique aqui]($link)\n" .
                "💵 Valor: {$preco}\n"
            );
        } else {
            $response = MessageBuilder::new()->setContent("Não encontrei esse jogo na Steam. 😕");
        }

        $interaction->respondWithMessage($response);
    });
});

$discord->run();