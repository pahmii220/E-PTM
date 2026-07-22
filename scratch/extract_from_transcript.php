<?php
$logFile = 'C:\\Users\\GAMING 3\\.gemini\\antigravity\\brain\\cfba2908-1a52-497e-898e-4246d51509fb\\.system_generated\\logs\\transcript_full.jsonl';
$lines = file($logFile);

$latestCreateContent = null;
$latestEditContent = null;

foreach ($lines as $line) {
    $data = json_decode($line, true);
    if ($data && isset($data['type']) && $data['type'] === 'PLANNER_RESPONSE' && isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $call) {
            if ($call['name'] === 'write_to_file' || $call['name'] === 'replace_file_content') {
                $args = $call['args'];
                $target = isset($args['TargetFile']) ? $args['TargetFile'] : '';
                
                if (strpos($target, 'deteksi_dini\\\\create.blade.php') !== false || strpos($target, 'deteksi_dini/create.blade.php') !== false) {
                    if ($call['name'] === 'write_to_file') {
                        $latestCreateContent = $args['CodeContent'];
                    }
                }
                if (strpos($target, 'deteksi_dini\\\\edit.blade.php') !== false || strpos($target, 'deteksi_dini/edit.blade.php') !== false) {
                    if ($call['name'] === 'write_to_file') {
                        $latestEditContent = $args['CodeContent'];
                    }
                }
            }
        }
    }
}

if ($latestCreateContent) {
    file_put_contents('C:\\laragon\\www\\E-PTM\\scratch\\recovered_create_2.blade.php', $latestCreateContent);
    echo "Recovered create.blade.php (" . strlen($latestCreateContent) . " bytes)\n";
} else {
    echo "Could not find write_to_file for deteksi_dini create.blade.php\n";
}

if ($latestEditContent) {
    file_put_contents('C:\\laragon\\www\\E-PTM\\scratch\\recovered_edit_2.blade.php', $latestEditContent);
    echo "Recovered edit.blade.php (" . strlen($latestEditContent) . " bytes)\n";
} else {
    echo "Could not find write_to_file for deteksi_dini edit.blade.php\n";
}
