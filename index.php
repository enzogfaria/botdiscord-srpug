<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <pre>
    <?php 
        
        $api_url = "https://store.steampowered.com/api/storesearch/?term=Aimlabs&cc=br&l=pt";   
        $dados = json_decode(file_get_contents($api_url), true);

        var_dump($dados);

        ?>
    </pre>
    
</body>
</html>