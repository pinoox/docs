<?php

$insert = file_get_contents(__DIR__ . '/cli-reference-insert-en.md');
$aliasBlock = <<<'TXT'
| `users` | `user:list` |
| `roles` | `role:list` |
| `permissions` | `permission:list` |
| `tokens` | `token:list` |
| `files` | `file:list` |
| `databases` | `db:list` |
| `make:permission` | `permission:create` |
TXT;
$related = <<<'TXT'
- [User management](../advanced/user-management.md)
- [Access & permissions](../advanced/access-permissions.md)
- [Token management](../advanced/token-management.md)
- [File management](../advanced/file-management.md)
TXT;

$locales = ['ar', 'de', 'es', 'fr', 'hi', 'ja', 'ko', 'pt', 'ru', 'tr', 'zh'];

foreach ($locales as $loc) {
    $path = dirname(__DIR__) . "/{$loc}/start/cli-reference.md";
    $content = file_get_contents($path);
    $eol = str_contains($content, "\r\n") ? "\r\n" : "\n";

    if (str_contains($content, 'db:list')) {
        echo "Skipped {$loc}\n";
        continue;
    }

    $routesNeedle = '| `routes` | `route:actions` |';
    $routesPos = strpos($content, $routesNeedle);
    if ($routesPos !== false && !str_contains($content, '| `users` | `user:list` |')) {
        $insertAt = $routesPos + strlen($routesNeedle);
        $content = substr($content, 0, $insertAt)
            . $eol . str_replace("\n", $eol, $aliasBlock)
            . substr($content, $insertAt);
    }

    $queryPos = strpos($content, '| `query` |');
    $cachePos = strpos($content, $eol . $eol . '---' . $eol . $eol . '## ', $queryPos ?: 0);
    if ($queryPos !== false && $cachePos !== false) {
        $insertAt = $cachePos;
        $content = substr($content, 0, $insertAt)
            . $eol . str_replace("\n", $eol, rtrim($insert))
            . substr($content, $insertAt);
    }

    if (!str_contains($content, 'user-management')) {
        $patchesPos = strpos($content, '../database/patches.md)');
        if ($patchesPos !== false) {
            $lineEnd = strpos($content, $eol, $patchesPos);
            if ($lineEnd !== false) {
                $content = substr($content, 0, $lineEnd + strlen($eol))
                    . str_replace("\n", $eol, $related) . $eol
                    . substr($content, $lineEnd + strlen($eol));
            }
        }
    }

    if (!str_contains($content, 'db:list')) {
        fwrite(STDERR, "Failed: {$loc}\n");
        continue;
    }

    file_put_contents($path, $content);
    echo "Updated {$loc}\n";
}
