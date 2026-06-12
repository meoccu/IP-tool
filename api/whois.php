<?php
header('Content-Type: application/json');
$query = $_GET['query'] ?? '';
if (!$query) {
    echo json_encode(['error' => 'Missing query parameter']);
    exit;
}
// 使用新的 whois 服务
$apiUrl = "https://whois.uoec.edu.kg/" . urlencode($query);
$data = @file_get_contents($apiUrl);
if ($data === false) {
    echo json_encode(['error' => 'Failed to fetch whois data']);
    exit;
}
// 直接返回 JSON
echo $data;