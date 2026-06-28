<?php
/**
 * Admin Get Logs Endpoint
 * Membaca berkas logs/error.log.php dan mengembalikan entri log terstruktur dalam format JSON.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Proteksi akses: wajib login admin mikhmon
if (!isset($_SESSION["mikhmon"])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$logFile = __DIR__ . '/../logs/error.log.php';
$log_entries = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Pastikan melewati baris pertama (PHP header protection)
    if (count($lines) > 1) {
        $data_lines = array_slice($lines, 1);
        
        // Ambil maksimal 30 log terakhir
        $recent_lines = array_slice($data_lines, -30);
        
        foreach ($recent_lines as $line) {
            // Format log: [Y-m-d H:i:s] [TYPE] [IP] Message
            if (preg_match('/^\[(.*?)\]\s+\[(.*?)\]\s+\[(.*?)\]\s+(.*)$/', $line, $matches)) {
                $log_entries[] = [
                    'time' => $matches[1],
                    'type' => $matches[2],
                    'ip' => $matches[3],
                    'message' => $matches[4]
                ];
            }
        }
    }
}

echo json_encode([
    'status' => 'success',
    'logs' => $log_entries
]);
exit;
