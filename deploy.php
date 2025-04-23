<?php

/**
 * Deploy Script (Windows-Compatible)
 * Copies all root contents into /deploy for FTP upload
 */

$deployDir = __DIR__ . '/deploy';

// List of files/folders to exclude from deployment
$exclude = ['deploy', '.git', '.gitignore', '.example.env', '.vscode', 'docs', '.env.production', '.env.local', 'README.md'];

// Recursively delete a folder
function deleteDir($dir)
{
    if (!file_exists($dir))
        return;

    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        is_dir($path) ? deleteDir($path) : unlink($path);
    }
    rmdir($dir);
}

// Recursively copy folder
function copyRecursive($src, $dst, $exclude)
{
    if (!is_dir($dst))
        mkdir($dst, 0777, true);
    $items = array_diff(scandir($src), ['.', '..']);

    foreach ($items as $item) {
        if (in_array($item, $exclude))
            continue;

        $srcPath = $src . '/' . $item;
        $dstPath = $dst . '/' . $item;

        if (is_dir($srcPath)) {
            copyRecursive($srcPath, $dstPath, $exclude);
        } else {
            copy($srcPath, $dstPath);
        }
    }
}

// Step 1: Remove old deploy folder
echo "Cleaning old deploy folder...\n";
deleteDir($deployDir);

// Step 2: Copy root files/folders into deploy
copyRecursive(__DIR__, $deployDir, $exclude);
copy(__DIR__ . '/.env.production', $deployDir . '/.env');


echo "\n✅ Deploy folder ready! Upload the contents of /deploy to your server via FTP.\n";
