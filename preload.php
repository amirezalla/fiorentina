<?php
// /var/www/html/preload.php
// Keep this side-effect free: do NOT run the app, only precompile files.

$files = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/bootstrap/app.php',
    // Hot paths (Composer’s optimized autoloader helps too)
    __DIR__ . '/vendor/composer/autoload_real.php',
    __DIR__ . '/vendor/composer/autoload_static.php',
    __DIR__ . '/vendor/composer/ClassLoader.php',
];

// Optionally add more frequently-used files/classes:
foreach ($files as $file) {
    if (is_file($file)) {
        @opcache_compile_file($file);
    }
}

// Optionally preload all files from Composer classmap:
$classmap = __DIR__ . '/vendor/composer/autoload_classmap.php';
if (is_file($classmap)) {
    $map = require $classmap;
    foreach ($map as $path) {
        if (is_string($path) && is_file($path)) {
            @opcache_compile_file($path);
        }
    }
}
