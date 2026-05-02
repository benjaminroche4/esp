<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

new Symfony\Component\Dotenv\Dotenv()->bootEnv(__DIR__ . '/../.env');

$kernel = new App\Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
