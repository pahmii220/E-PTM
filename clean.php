<?php
$lines = file('resources/views/kepala_p2ptm/dashboard.blade.php');
$output = [];
foreach ($lines as $i => $line) {
    $n = $i + 1;
    if ($n >= 61 && $n <= 111) continue;
    if ($n >= 112 && $n <= 114) continue;
    if ($n >= 356 && $n <= 593) continue;
    $output[] = $line;
}
file_put_contents('resources/views/kepala_p2ptm/dashboard.blade.php', implode('', $output));
echo "Done";
