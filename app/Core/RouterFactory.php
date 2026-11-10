<?php declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router->addRoute('prihlaseni', 'Sign:in');
        $router->addRoute('predmety', 'Course:default');
        $router->addRoute('terminy/novy', 'Term:create');
        $router->addRoute('terminy/<id \d+>/upravit', 'Term:edit');
        $router->addRoute('terminy/<id \d+>/smazat', 'Term:delete');
        $router->addRoute('terminy/<id \d+>', 'Term:detail');
        $router->addRoute('terminy', 'Term:default');
        $router->addRoute('', 'Term:default');
        return $router;
    }
}
