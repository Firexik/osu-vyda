<?php declare(strict_types=1);

use Nette\Application\Application;
use Nette\Bootstrap\Configurator;

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$configurator = new Configurator;
$configurator->setTempDirectory($root . '/temp');
$configurator->addConfig($root . '/config/common.neon');
if (is_file($root . '/config/local.neon')) {
    $configurator->addConfig($root . '/config/local.neon');
}

$configurator->createContainer()->getByType(Application::class)->run();
