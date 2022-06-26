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
$router->get('/', function ()  {
    return view('index');
});

$router->get('/api/recipes',['middlewares' => 'auth', 'uses' => 'RecipeController@all']);

$router->get('/api/recipes/{id}', ['middlewares' => 'auth', 'uses' => 'RecipeController@get']);
$router->delete('/api/recipes/{id}', ['middlewares' => 'auth', 'uses' => 'RecipeController@delete']);

$router->post('/api/recipes/',  ['middlewares' => 'auth', 'uses' => 'RecipeController@create']);

$router->get('ini', function(){
    return phpinfo();
});

