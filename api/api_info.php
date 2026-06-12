<?php
header('Content-Type: application/json');
$sources = [
    ['url' => 'https://api.ipify.org?format=json', 'name' => 'ipify IPv4', 'type' => 'IPv4'],
    ['url' => 'https://api6.ipify.org?format=json', 'name' => 'ipify IPv6', 'type' => 'IPv6'],
    ['url' => 'https://ipinfo.io/json', 'name' => 'ipinfo.io', 'type' => 'IPv4/IPv6'],
    ['url' => 'http://ip-api.com/json/?fields=query', 'name' => 'ip-api.com', 'type' => 'IPv4'],
];
$results = [];
foreach ($sources as $src) {
    $ip = null;
    try {
        $ctx = stream_context_create(['http' => ['timeout' => 5]]);
        $data = @file_get_contents($src['url'], false, $ctx);
        if ($data) {
            $json = json_decode($data, true);
            $ip = $json['ip'] ?? $json['query'] ?? null;
        }
    } catch (Exception $e) {}
    $results[] = [
        'name' => $src['name'],
        'type' => $src['type'],
        'ip' => $ip,
    ];
}
echo json_encode(['sources' => $results]);