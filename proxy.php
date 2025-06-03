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
    'example.com',
    'w00x-cdn.pages.dev'
];

$parsedUrl = parse_url($url);
if (!$parsedUrl || !isset($parsedUrl['host']) || !in_array($parsedUrl['host'], $allowedDomains)) {
    http_response_code(403);
    echo "Erişim engellendi: İzin verilmeyen domain.";
    exit;
}

// Yayın dosyası olması gereken uzantılar (geliştirilebilir)
$allowedExtensions = ['m3u8', 'ts', 'aac', 'mp3', 'mp4'];
$path = $parsedUrl['path'] ?? '';
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    http_response_code(415);
    echo "Hata: Desteklenmeyen dosya türü veya yayın dosyası değil.";
    exit;
}

// Gelişmiş headerlar
$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
    'Referer: https://' . $parsedUrl['host'] . '/',
    'Accept: */*',
    'Accept-Language: en-US,en;q=0.9',
    'Connection: keep-alive',
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_ENCODING, ''); // tüm encodingleri destekle
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL sorunlarını yoksay
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if(curl_errno($ch)) {
    http_response_code(502);
    echo "Proxy Hatası: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Gelen içerik tipi kontrolü (opsiyonel)
if (strpos($contentType, 'application/vnd.apple.mpegurl') === false && 
    strpos($contentType, 'video') === false && 
    strpos($contentType, 'audio') === false &&
    strpos($contentType, 'text') === false) {
    http_response_code(415);
    echo "Hata: Beklenmeyen içerik tipi: $contentType";
    exit;
}

http_response_code($httpCode);
header('Content-Type: ' . $contentType);
echo $response;
