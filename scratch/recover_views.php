<?php
$logFile = 'C:\\Users\\GAMING 3\\.gemini\\antigravity\\brain\\cfba2908-1a52-497e-898e-4246d51509fb\\.system_generated\\logs\\transcript_full.jsonl';
$lines = file($logFile);

$createContent = '';
$editContent = '';

foreach ($lines as $line) {
    $data = json_decode($line, true);
    if ($data && isset($data['type']) && $data['type'] === 'PLANNER_RESPONSE' && isset($data['tool_calls'])) {
        // we need to look at tool responses, actually. Tool responses are in 'tool_calls' of the NEXT step? No, tool responses are usually in a different step type or embedded.
        // Wait, transcript format: "tool_calls" has args, but where are the responses?
        // Ah, responses are in `tool_responses` array maybe? Or `type: TOOL_RESPONSE`?
        // Let's just search the string content of the whole line.
    }
}
