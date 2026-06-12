<?php
header('Content-Type: application/json');
$url = $_GET['url'] ?? '';
$country = $_GET['country'] ?? 'CN';
if (empty($url)) { echo json_encode(['error' => 'Missing url']); exit; }
// 使用 api.greatfire.org 或类似服务
$api = "https://api.greatfire.org/check?url=" . urlencode($url);
$result = @file_get_contents($api);
if ($result) {
    $data = json_decode($result, true);
    $blocked = $data['blocked'] ?? false;
    echo json_encode(['blocked' => $blocked, 'message' => $blocked ? '该网站在目标国家可能被封锁' : '可访问']);
} else {
    // 简单模拟：如果国家为CN且域名包含twitter等，返回封锁
    $blocked = ($country === 'CN' && preg_match('/twitter|facebook|google/i', $url));
    echo json_encode(['blocked' => $blocked, 'message' => '基于规则猜测']);
}