<?php
// Local PHP simulation of webhook handler (for environments without bash)
$logFile = __DIR__ . '/../webhook.test.log';

$body = null;
if (!empty($argv[1])) {
    $body = $argv[1];
} else {
    $body = stream_get_contents(STDIN);
}

if (empty($body)) {
    echo "No payload provided\n";
    exit(1);
}

file_put_contents($logFile, date('Y-m-d H:i:s') . " - [PHP TEST] Webhook received\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - [PHP TEST] Payload: " . $body . "\n", FILE_APPEND);

$data = json_decode($body, true);
$ref = $data['ref'] ?? null;
$branch = null;
if ($ref) {
    $branch = preg_replace('#refs/heads/#', '', $ref);
}
file_put_contents($logFile, date('Y-m-d H:i:s') . " - [PHP TEST] Branch: " . ($branch ?? 'null') . "\n", FILE_APPEND);

if ($branch === 'main') {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - [PHP TEST] Triggering dummy update...\n", FILE_APPEND);
    // Simulate background job by appending start/end
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - DUMMY UPDATE START (main)\n", FILE_APPEND);
    usleep(200000);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - DUMMY UPDATE END (main)\n", FILE_APPEND);
    echo "Deployment started\n";
} else {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - [PHP TEST] Ignored - not main branch\n", FILE_APPEND);
    echo "Ignored - not main branch\n";
}
