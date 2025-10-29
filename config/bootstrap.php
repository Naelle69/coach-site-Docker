<?php
// config/bootstrap.php
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (is_array($env = @include dirname(__DIR__).'/.env.local.php')) {
    $_SERVER += $env;
    $_ENV += $env;
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files.');
} else {
    // Charge .env, puis .env.local, et selon APP_ENV charge .env.test / .env.dev, etc.
    (new Dotenv())->usePutenv()->bootEnv(dirname(__DIR__).'/.env');
}
