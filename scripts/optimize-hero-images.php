<?php

use App\Support\ImageHelper;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dir = public_path('images/hero');
$files = array_values(array_filter(scandir($dir) ?: [], function ($f) use ($dir) {
    if ($f === '.' || $f === '..' || $f === 'README.txt') {
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
    $source = 'images/hero/'.$file;
    $dest = 'images/hero/portfolio-'.$index.'.webp';
    $ok = ImageHelper::convertPublicToWebp($source, $dest, 840, 82);
    echo ($ok ? 'OK' : 'SKIP')." {$file} -> portfolio-{$index}.webp\n";
    $index++;
}

echo "Done. ".($index - 1)." images processed.\n";
