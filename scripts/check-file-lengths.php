<?php

$productionRoots = ['app', 'bootstrap', 'config', 'database', 'lang', 'public', 'resources', 'routes', 'scripts'];
$extensions = ['php', 'blade.php', 'js', 'css', 'py'];
$violations = [];

foreach ($productionRoots as $root) {
    if (! is_dir($root)) {
        continue;
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($files as $file) {
        if (! $file->isFile() || ! isCodeFile($file->getFilename(), $extensions)) {
            continue;
        }

        $path = str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname());
        if (str_contains($path, '/cache/') || str_contains($path, '/vendor/')) {
            continue;
        }

        $limit = str_ends_with($path, '.py') ? 400 : 300;
        recordViolation($violations, $path, lineCount($path), $limit);
    }
}

if (is_dir('tests')) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('tests'));
    foreach ($files as $file) {
        if ($file->isFile() && isCodeFile($file->getFilename(), $extensions)) {
            recordViolation($violations, $file->getPathname(), lineCount($file->getPathname()), 1000);
        }
    }
}

if ($violations !== []) {
    fwrite(STDERR, "File length limits exceeded:\n".implode("\n", $violations)."\n");
    exit(1);
}

fwrite(STDOUT, "File length limits passed.\n");

function isCodeFile(string $name, array $extensions): bool
{
    foreach ($extensions as $extension) {
        if (str_ends_with($name, '.'.$extension)) {
            return true;
        }
    }

    return false;
}

function lineCount(string $path): int
{
    $lines = file($path);

    return $lines === false ? 0 : count($lines);
}

function recordViolation(array &$violations, string $path, int $lines, int $limit): void
{
    if ($lines > $limit) {
        $violations[] = sprintf('%s: %d lines (limit %d)', $path, $lines, $limit);
    }
}
