<?php
foreach (glob(__DIR__ . '/storage/app/public/ebooks/*.txt') as $f) {
    echo basename($f) . ': ' . filesize($f) . "\n";
}
