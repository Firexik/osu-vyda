<?php declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

final class Bootstrap
{
    public static function boot(): Container
    {
        date_default_timezone_set('Europe/Prague');
        $root = dirname(__DIR__);
        foreach (['temp', 'log', 'temp/sessions'] as $directory) {
            if (!is_dir($root . '/' . $directory)) {
                mkdir($root . '/' . $directory, 0770, true);
            }
        }

        $configurator = new Configurator;
        // Explicitly opt in on the local machine; errors never expose credentials by default.
        $configurator->setDebugMode(getenv('APP_DEBUG') === '1');
        $configurator->enableTracy($root . '/log');
        $configurator->setTempDirectory($root . '/temp');
        $configurator->createRobotLoader()->addDirectory(__DIR__)->register();
        $configurator->addConfig($root . '/config/common.neon');
        if (is_file($root . '/config/local.neon')) {
            $configurator->addConfig($root . '/config/local.neon');
        }
        return $configurator->createContainer();
    }
}
