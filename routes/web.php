<?php

use App\APIVersioning\Versions\Version1 as V1;
use Laravel\Lumen\Routing\Router;

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//Return error for all path except those defined ones
//$router->get('*', function () {
//    //return $router->app->version();
//    return 'hello world!';
//});

//Add route for version 1 APIs
$router->group(['prefix' => 'v1','as' => 'v1'], function () use ($router) {
    V1::renderRoutes($router);
});
