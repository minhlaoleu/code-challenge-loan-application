<?php declare(strict_types=1);

use Laravel\Lumen\Routing\Router;

/**
 * @var Router $router
 */

$router->post('/register', [ 'as' => 'register', 'uses' => 'UserController@register']);
$router->post('/login', [ 'as' => 'login', 'uses' => 'UserController@login']);

/**
 * Guarded group
 */
$router->group(['middleware' => 'auth'], function () use ($router) {
    /**
     * Log user out
     */
    $router->get('/logout', [ 'as' => 'logout', 'uses' => 'UserController@logout']);

    /**
     * Refresh token
     */
    $router->get('/refresh-token', [ 'as' => 'refreshToken', 'uses' => 'UserController@refresh']);
});
