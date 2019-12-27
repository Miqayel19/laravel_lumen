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
$router->put('user/{id}','UserController@update');
$router->delete('user/{id}','UserController@delete');

$router->post('team','TeamController@add');
$router->get('team/{id}','TeamController@show');
$router->put('team/{id}','TeamController@update');
$router->delete('team/{id}','TeamController@delete');


$router->post('add_team_member/user/{member_id}/team/{team_id}','TeamController@addTeamMember');
$router->post('add_team_owner/user/{owner_id}/team/{team_id}','TeamController@addTeamOwner');


$router->delete('delete_team_member/user/{member_id}/team/{team_id}','TeamController@deleteTeamMember');
$router->delete('delete_team_owner/user/{owner_id}/team/{team_id}','TeamController@deleteTeamOwner');



