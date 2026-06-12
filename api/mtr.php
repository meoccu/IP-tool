<?php
header('Content-Type: text/plain');
$target = escapeshellarg($_GET['target'] ?? '8.8.8.8');
// 需要服务器安装 mtr，若没有则用 traceroute
$cmd = "mtr -r -c 1 -n {$target} 2>&1 || traceroute -n -m 15 {$target} 2>&1";
echo shell_exec($cmd);