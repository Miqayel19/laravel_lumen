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

$router->post('users','UserController@add');
$router->get('users/{id}','UserController@index');
$router->get('users','UserController@show');
$router->put('users/{id}','UserController@update');
$router->delete('users/{id}','UserController@delete');

$router->post('teams','TeamController@add');
$router->get('teams/{id}','TeamController@index');
$router->get('teams','TeamController@show');
$router->put('teams/{id}','TeamController@update');
$router->delete('teams/{id}','TeamController@delete');


$router->post('add_team_member/user/{member_id}/team/{team_id}','TeamController@addTeamMember');
$router->post('add_team_owner/user/{owner_id}/team/{team_id}','TeamController@addTeamOwner');


$router->delete('delete_team_member/user/{member_id}/team/{team_id}','TeamController@deleteTeamMember');
$router->delete('delete_team_owner/user/{owner_id}/team/{team_id}','TeamController@deleteTeamOwner');



