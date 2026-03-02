<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$book = App\Models\Ebook::where('file_type', 'txt')->latest()->first();
if (!$book) {
    die("No txt book found\n");
}
$path = storage_path('app/public/ebooks/' . $book->filename);
echo "Path: $path\n";
if (!file_exists($path)) {
    die("File does not exist\n");
}
$content = file_get_contents($path);
echo "Length: " . strlen($content) . "\n";
echo "First 20 bytes: " . bin2hex(substr($content, 0, 20)) . "\n";
echo "Encoding: " . mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16LE', 'UTF-16BE']) . "\n";
