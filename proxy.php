<?php
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo "Hata: url parametresi eksik.";
    exit;
}

$url = $_GET['url'];

// İzin verilen domainler
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
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$response = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);

if (strpos($contentType, 'application/vnd.apple.mpegurl') !== false || strpos($contentType, 'application/x-mpegURL') !== false) {
    // .m3u8 dosyasını proxy’le ve segment URL’lerini proxy’ye çevir

    $baseUrl = rtrim(dirname($url), '/') . '/';
    $lines = explode("\n", $response);
    foreach ($lines as &$line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#') && (strpos($line, '.ts') !== false || strpos($line, '.m3u8') !== false)) {
            // Göreceli ise tam yap
            if (!preg_match('#^https?://#', $line)) {
                $line = $baseUrl . $line;
            }
            // Proxy URL yap
            $line = 'proxy.php?url=' . urlencode($line);
        }
    }
    $response = implode("\n", $lines);

    header('Content-Type: application/vnd.apple.mpegurl');
} elseif (strpos($contentType, 'video/MP2T') !== false || strpos($contentType, 'application/octet-stream') !== false) {
    // .ts segmentleri için doğru Content-Type
    header('Content-Type: video/MP2T');
} else {
    header('Content-Type: ' . $contentType);
}

echo $response;
