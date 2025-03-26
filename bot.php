<?php 
include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;

$discord = new Discord([
    'token' => 'MTM1NDUxMzYwNDYxMDg4Nzc3Mg.G8yjcS.hznvVY8ZYEYOcZh8pYfPIjTqxcI1vF3T6x6NDU',
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
     // Note: MESSAGE_CONTENT is privileged, see https://dis.gd/mcfaq
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot is ready!", PHP_EOL;

    // Ver mensagens.
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {
        if(strpos($message->content, '!preço') === 0){
            $jogo = trim(str_replace('!preço',  '', $message->content));

            if(empty($jogo)){
                $message->reply("❌ Por favor, insira o nome do jogo após `!preço`. Exemplo: `!preço Hollow Knight`");
                return; 
            }

            $api_url = "https://store.steampowered.com/api/storesearch/?term=" . urlencode($jogo) . "&cc=br&l=pt";   
            $dados = json_decode(file_get_contents($api_url), true);

            if(!empty($dados['items'])){
                $primeiroJogo = $dados['items'][0];
                $nome = $primeiroJogo['name'];
                $preco = isset($primeiroJogo['price']['final']) ? 'R$ ' . number_format($primeiroJogo['price']['final'] / 100, 2, ',', '.') : 'Jogo Gratuito';
                $link = "https://store.steampowered.com/app/" . $primeiroJogo['id'];

                $message->reply("🎮 **$nome** está custando **$preco** na Steam! 🔗 [Clique aqui]($link)");
            } else {
                $message->reply("Não encontrei esse jogo na Steam. 😕");
            }
        }
    });



    $discord->run();
});




?>