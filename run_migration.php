<?php
// Run Laravel migration

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'migrate'
    ]),
    new \Symfony\Component\Console\Output\BufferedOutput
);

if ($status === 0) {
    echo "✅ Migration berhasil! Tabel 'tugas' telah dibuat.\n\n";
    
    // Clear cache
    $kernel->handle(
        new \Symfony\Component\Console\Input\ArrayInput(['command' => 'config:cache']),
        new \Symfony\Component\Console\Output\BufferedOutput
    );
    echo "✅ Cache cleared!\n\n";
    
    echo "🎉 Setup selesai! Silakan akses: http://localhost/lapangan/tugas\n";
} else {
    echo "❌ Migration gagal. Status: $status\n";
    echo "Debug: Periksa connection ke database.\n";
}
?>
