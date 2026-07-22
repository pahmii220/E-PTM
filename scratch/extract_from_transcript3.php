<?php
$logFile = 'C:\\Users\\GAMING 3\\.gemini\\antigravity\\brain\\cfba2908-1a52-497e-898e-4246d51509fb\\.system_generated\\logs\\transcript_full.jsonl';
$lines = file($logFile);

$latestCreate = null;
$latestEdit = null;
$createPath = 'C:\\laragon\\www\\E-PTM\\resources\\views\\petugas\\deteksi_dini\\create.blade.php';
$editPath = 'C:\\laragon\\www\\E-PTM\\resources\\views\\petugas\\deteksi_dini\\edit.blade.php';

foreach ($lines as $line) {
    $data = json_decode($line, true);
    if ($data && isset($data['type']) && $data['type'] === 'PLANNER_RESPONSE' && isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $call) {
            if ($call['name'] === 'write_to_file') {
                $target = rtrim($call['args']['TargetFile'] ?? '');
                
                if (strtolower($target) === strtolower($createPath)) {
                    $latestCreate = $call['args']['CodeContent'];
                }
                if (strtolower($target) === strtolower($editPath)) {
                    $latestEdit = $call['args']['CodeContent'];
                }
            }
        }
    }
}

if ($latestCreate) {
    file_put_contents('C:\\laragon\\www\\E-PTM\\scratch\\recovered_create_final.blade.php', $latestCreate);
    echo "Found create.blade.php (" . strlen($latestCreate) . " bytes)\n";
} else {
    echo "Could not find write_to_file for create\n";
}

if ($latestEdit) {
    file_put_contents('C:\\laragon\\www\\E-PTM\\scratch\\recovered_edit_final.blade.php', $latestEdit);
    echo "Found edit.blade.php (" . strlen($latestEdit) . " bytes)\n";
} else {
    echo "Could not find write_to_file for edit\n";
}
