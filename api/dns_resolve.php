<?php
header('Content-Type: application/json');
$domain = $_GET['domain'] ?? '';
if (!$domain) { echo json_encode(['error'=>'No domain']); exit; }
$records = [];
$dns = @dns_get_record($domain, DNS_A);
if ($dns) {
    foreach ($dns as $rec) $records[] = $rec['ip'];
}
echo json_encode(['records' => $records]);