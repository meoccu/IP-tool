<?php
header('Content-Type: application/json');
// 模拟访问外部检测服务返回IP
$servers = [
    'ip-api' => 'http://ip-api.com/json/?fields=query',
    'ipleak' => 'https://ipleak.net/json/',
];
$results = [];
foreach ($servers as $name => $url) {
    $ip = null;
    try {
        $data = @file_get_contents($url, false, stream_context_create(['http'=>['timeout'=>5]]));
        $json = json_decode($data, true);
        $ip = $json['query'] ?? $json['ip'] ?? null;
    } catch (Exception $e) {}
    $results[] = ['server' => $name, 'ip' => $ip ?? '检测失败'];
}
echo json_encode(['results' => $results]);