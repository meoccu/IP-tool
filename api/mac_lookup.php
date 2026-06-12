<?php
header('Content-Type: application/json');
$mac = $_GET['mac'] ?? '';
if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac)) {
    echo json_encode(['error' => 'Invalid MAC']);
    exit;
}
$vendor = @file_get_contents("https://api.macvendors.com/" . urlencode($mac));
echo json_encode(['vendor' => $vendor ?: '未知']);