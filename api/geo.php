<?php
header('Content-Type: application/json');
$ip = $_GET['ip'] ?? $_SERVER['REMOTE_ADDR'];
$geo = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country,regionName,city,isp");
if ($geo) {
    echo $geo;
} else {
    echo json_encode(['error' => '无法获取位置']);
}