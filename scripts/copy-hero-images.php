<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dir = public_path('images/hero');
$files = array_values(array_filter(scandir($dir) ?: [], function ($f) {
    if ($f === '.' || $f === '..' || $f === 'README.txt') {
        return false;
    }
    if (preg_match('/^portfolio-\d+\.(jpg|jpeg|webp|png)$/i', $f)) {
        return false;
    }
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));

    return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
}));

usort($files, function ($a, $b) {
    $aNum = preg_match('/\((\d+)\)/', $a, $am) ? (int) $am[1] : 0;
    $bNum = preg_match('/\((\d+)\)/', $b, $bm) ? (int) $bm[1] : 0;
    if ($aNum === $bNum) {
        return strnatcasecmp($a, $b);
    }

    return $aNum <=> $bNum;
});

$index = 1;
foreach ($files as $file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)) ?: 'jpg';
    $dest = "portfolio-{$index}.{$ext}";
    $from = $dir.DIRECTORY_SEPARATOR.$file;
    $to = $dir.DIRECTORY_SEPARATOR.$dest;
    if (copy($from, $to)) {
        echo "OK {$file} -> {$dest}\n";
    } else {
        echo "FAIL {$file}\n";
    }
    $index++;
}

echo 'Done. '.($index - 1)." files copied.\n";
