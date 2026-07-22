<?php
$logFile = 'C:\\Users\\GAMING 3\\.gemini\\antigravity\\brain\\cfba2908-1a52-497e-898e-4246d51509fb\\.system_generated\\logs\\transcript_full.jsonl';
$lines = file($logFile);

foreach ($lines as $line) {
    $data = json_decode($line, true);
    // Tool responses are typically in a SYSTEM message or in 'output'
    if ($data && isset($data['content']) && strpos($data['content'], 'deteksi_dini/create.blade.php') !== false) {
        if (strpos($data['content'], 'Total Lines: 5') !== false || strpos($data['content'], 'Showing lines') !== false) {
            file_put_contents('C:\\laragon\\www\\E-PTM\\scratch\\view_file_dump.txt', $data['content'] . "\n\n=======================\n\n", FILE_APPEND);
        }
    }
}
echo "Dumped view_file outputs to scratch/view_file_dump.txt\n";
