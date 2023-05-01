<?php declare(strict_types=1);

namespace App\APIVersioning\Versions;

use App\APIVersioning\VersioningAbstract;
use App\APIVersioning\VersioningInterface;
use Laravel\Lumen\Routing\Router;

class Version1 extends VersioningAbstract implements VersioningInterface
{
    public static function renderRoutes(Router $router): void
    {
        $versionBasePath =  glob(app()->basePath() . '/routes/v1/*.php');
        foreach ($versionBasePath as $filename)
        {
            require $filename;
        }
    }
}
