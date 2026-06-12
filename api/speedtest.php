<?php
header('Content-Type: application/json');

// 允许前端指定大小 (MB)，默认 50
$sizeMB = intval($_GET['size'] ?? 50);
// 限制大小范围
$sizeMB = max(1, min($sizeMB, 200));
$sizeBytes = $sizeMB * 1024 * 1024;

// 多源测速节点配置
$sources = [
    'tencent' => [
        'name' => '腾讯云',
        // 腾讯QQ安装包，支持Range，可根据大小截取
        'url' => 'https://dldir1.qq.com/qqfile/qq/PCQQ9.7.17/QQ9.7.17.29225.exe',
        'max_file_size' => 210 * 1024 * 1024, // 实际文件约210MB
    ],
    'aliyun' => [
        'name' => '阿里云',
        'url' => 'https://aliyun-common.oss-cn-hangzhou.aliyuncs.com/speed-test/100mb.bin',
        'max_file_size' => 100 * 1024 * 1024,
    ],
    'huawei' => [
        'name' => '华为云',
        'url' => 'https://obs.cn-north-1.myhuaweicloud.com/speed-test/100mb.bin',
        'max_file_size' => 100 * 1024 * 1024,
    ],
    'xunlei' => [
        'name' => '迅雷',
        'url' => 'https://down.sandai.net/thunder11/XunLeiWebSetup11.4.8.2018.exe',
        'max_file_size' => 120 * 1024 * 1024, // 约120MB
    ],
    'cloudflare' => [
        'name' => 'Cloudflare',
        'url' => 'https://speed.cloudflare.com/__down?bytes=' . $sizeBytes,
        'max_file_size' => $sizeBytes,
    ],
];

// 获取前端指定的源，不指定则自动选择
$sourceParam = $_GET['source'] ?? 'auto';

// 尝试下载并测速的函数
function testDownloadSpeed($url, $sizeBytes, $maxFileSize) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RANGE => "0-" . ($sizeBytes - 1), // 请求指定字节数
        CURLOPT_NOBODY => false,
    ]);

    $startTime = microtime(true);
    $data = curl_exec($ch);
    $endTime = microtime(true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $downloadSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
    $err = curl_error($ch);
    curl_close($ch);

    $duration = $endTime - $startTime;
    $speedMbps = ($duration > 0 && $downloadSize > 0) ? ($downloadSize * 8 / $duration / 1000000) : 0;

    return [
        'success' => ($httpCode >= 200 && $httpCode < 400 && !$err),
        'speed' => round($speedMbps, 2),
        'latency' => round($duration * 1000, 1),
        'downloaded_bytes' => $downloadSize,
        'error' => $err ?: null,
    ];
}

$results = [];
$bestSpeed = 0;
$bestResult = null;

// 如果指定了有效源，只测该源
if (isset($sources[$sourceParam])) {
    $src = $sources[$sourceParam];
    $maxSize = min($sizeBytes, $src['max_file_size']);
    $results[] = testDownloadSpeed($src['url'], $maxSize, $src['max_file_size']);
    $bestResult = $results[0];
} else {
    // 自动模式：测试所有源，选最快的
    foreach ($sources as $key => $src) {
        $maxSize = min($sizeBytes, $src['max_file_size']);
        $res = testDownloadSpeed($src['url'], $maxSize, $src['max_file_size']);
        $res['source'] = $src['name'];
        $results[$key] = $res;
        if ($res['success'] && $res['speed'] > $bestSpeed) {
            $bestSpeed = $res['speed'];
            $bestResult = $res;
        }
    }
}

// 上传速度仍为模拟，基于最佳下载速度估算
$uploadSpeed = $bestResult ? round($bestResult['speed'] * 0.7, 2) : 0;
$latency = $bestResult['latency'] ?? 999;

// 构建响应
$response = [
    'download' => $bestResult ? $bestResult['speed'] : 0,
    'upload' => $uploadSpeed,
    'latency' => $latency,
    'tested_size_mb' => $sizeMB,
    'source' => $sourceParam === 'auto' ? 'auto (fastest)' : $sourceParam,
    'all_results' => $results,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);