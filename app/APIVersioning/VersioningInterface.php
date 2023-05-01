<?php declare(strict_types=1);

namespace App\APIVersioning;

use Laravel\Lumen\Routing\Router;

interface VersioningInterface
{
    /**
     * Use to group all the version APIs to folder group
     * @param Router $router
     * @return void
     */
    public static function renderRoutes(Router $router): void;
}
