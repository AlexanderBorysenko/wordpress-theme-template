<?php
/**
 * Includes all PHP files from specified folders with customizable pattern matching.
 *
 * @param string ...$paths List of folders/files to include. The last argument can be a glob pattern.
 * @return int Number of files included
 */
function requireAll(string ...$paths): int
{
    $pattern       = '*.php';
    $includedCount = 0;

    // If the last argument is a glob pattern (contains a wildcard), use it.
    if (!empty($paths) && strpos(end($paths), '*') !== false) {
        $pattern = array_pop($paths);
    }

    foreach ($paths as $path) {
        if (isFilePath($path)) {
            require_once get_template_directory() . "/$path";
            $includedCount++;
            continue;
        }

        $fullPath = get_template_directory() . "/$path";
        $files    = glob("$fullPath/$pattern") ?: [];

        if (empty($files)) {
            continue;
        }

        $sortedFiles = sortFilesByNameStructure($files);

        foreach ($sortedFiles as $file) {
            require_once $file;
            $includedCount++;
        }
    }

    return $includedCount;
}

/**
 * Determines if a path refers to a file (contains a dot).
 *
 * @param string $path Path to check
 * @return bool True if the path looks like a file path
 */
function isFilePath(string $path): bool
{
    return strpos($path, '.') !== false;
}

/**
 * Sorts files by their name structure—first by dot count, then by root name.
 * 
 * @param array $files Array of file paths to sort
 * @return array Sorted array of file paths
 */
function sortFilesByNameStructure(array $files): array
{
    usort(
        $files,
        fn(string $a, string $b): int =>
        ($dotsA = substr_count(basename($a), '.')) !== ($dotsB = substr_count(basename($b), '.'))
        ? $dotsA <=> $dotsB
        : getRootName(basename($a)) <=> getRootName(basename($b))
    );

    return $files;
}

/**
 * Extracts the root name from a filename (part before the first dot).
 *
 * @param string $filename The filename
 * @return string Root name
 */
function getRootName(string $filename): string
{
    $dotPos = strpos($filename, '.');
    return $dotPos !== false ? substr($filename, 0, $dotPos) : $filename;
}
