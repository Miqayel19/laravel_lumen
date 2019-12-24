<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('user','UserController@add');
$router->get('user/{id}','UserController@show');
$router->delete('user/{id}','UserController@delete');

$router->get('team/{id}','TeamController@index');
$router->post('team','TeamController@add');
$router->put('team/{id}','TeamController@update');
$router->delete('team/{id}','TeamController@delete');
$router->delete('delete_role_team/{id}','TeamController@deleteRole');



