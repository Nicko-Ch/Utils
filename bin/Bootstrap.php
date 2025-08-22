#!/usr/bin/env php
<?php

// load dependencies
(static function () {

    // OK, it's not, let give Composer autoloader a try!
    $possibleFiles = [__DIR__ . '/../../autoload.php', __DIR__ . '/../autoload.php', __DIR__ . '/vendor/autoload.php'];
    $file          = null;
    foreach ($possibleFiles as $possibleFile) {
        if (file_exists($possibleFile)) {
            $file = $possibleFile;

            break;
        }
    }

    if (null === $file) {
        throw new RuntimeException('Unable to locate autoload.php file.');
    }

    require_once $file;
})();