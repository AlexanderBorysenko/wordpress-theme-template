<?php
function includePhpFiles(string $directory): void
{
    // Include all PHP files in the current directory
    foreach (glob("{$directory}/*.php") as $helperFile) {
        // echo "Including {$helperFile} <br>";
        require_once $helperFile;
    }

    // Process subdirectories recursively
    foreach (glob("{$directory}/*", GLOB_ONLYDIR) as $subdirectory) {
        includePhpFiles(path_join(__DIR__, $subdirectory));
    }
}

// Recursively find and include files from specific folder types at any depth
function findAndIncludeSpecificFolders(string $baseDir, array $targetFolderNames): void
{
    // Process the current directory for target folders
    foreach ($targetFolderNames as $folderName) {
        if (is_dir("{$baseDir}/{$folderName}")) {
            includePhpFiles("{$baseDir}/{$folderName}");
        }
    }

    // Check subdirectories
    foreach (glob("{$baseDir}/*", GLOB_ONLYDIR) as $subdir) {
        findAndIncludeSpecificFolders($subdir, $targetFolderNames);
    }
}

includePhpFiles(path_join(__DIR__, 'Helpers'));

// Find and include Helpers and shortcuts folders at any depth
findAndIncludeSpecificFolders(__DIR__ . '/Services', [
    'helpers',
    'shortcuts'
]);
