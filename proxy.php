<?php
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo "Hata: url parametresi eksik.";
    exit;
}

$url = $_GET['url'];

// URL güvenliği için izin verilen domain kontrolü
$allowedDomains = [
    'lll.istekbet3.tv',
    'example.com'
];

$parsedUrl = parse_url($url);
if (!$parsedUrl || !in_array($parsedUrl['host'], $allowedDomains)) {
    http_response_code(403);
    echo "Erişim engellendi.";
    exit;
}

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'Referer: https://' . $parsedUrl['host'] . '/'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$response = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header('Content-Type: ' . $contentType);
echo $response;
