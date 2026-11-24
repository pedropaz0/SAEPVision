<?php
require_once __DIR__ . '/Config/configuration.php';

spl_autoload_register(function (string $classe): void {
    $arquivo = __DIR__ . '/' . str_replace('\\', '/', $classe) . '.php';

    if (is_file($arquivo)) {
        require_once $arquivo;
    }
});
