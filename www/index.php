<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$container = App\Booting::boot()
    ->createContainer();

$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
if ($origin !== null && in_array($origin, $container->getParameters()['allowedOrigins'], true)) {
    header('Access-Control-Allow-Headers: accept, content-type');
    header('Access-Control-Allow-Methods: GET,POST,OPTIONS,DELETE,PUT');
    header('Access-Control-Allow-Origin: ' . $origin);
    if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
        die;
    }
}

$container
    ->getByType(Nette\Application\Application::class)
    ->run();
