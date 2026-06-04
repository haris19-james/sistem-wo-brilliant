<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$slides = App\Support\Branding::heroGallerySlides();
echo count($slides)." slides\n";
foreach ($slides as $i => $s) {
    echo ($i+1).". ".$s['src']."\n";
}
