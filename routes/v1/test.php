<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * @var Router $router;
 */


$router->get('/ping', [ 'as' => 'ping', function () {
    return 'pong';
}]);

$router->get('/version', [ 'as' => 'version', function () use ($router) {
    return $router->app->version();
}]);
