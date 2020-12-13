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

$router->group(['prefix' => 'Aoc'], function() use ($router) {
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

    $router->group(['prefix' => '2020'], function() use ($router) {
        $router->get('day/1[/part/{part}]', Day1Controller::class);
        $router->get('day/2[/part/{part}]', Day2Controller::class);
        $router->get('day/3[/part/{part}]', Day3Controller::class);
        $router->get('day/4[/part/{part}]', Day4Controller::class);
        $router->get('day/5[/part/{part}]', Day5Controller::class);
        $router->get('day/6[/part/{part}]', Day6Controller::class);
        $router->get('day/7[/part/{part}]', Day7Controller::class);
        $router->get('day/8[/part/{part}]', Day8Controller::class);
        $router->get('day/9[/part/{part}]', Day9Controller::class);
        $router->get('day/10[/part/{part}]', Day10Controller::class);
        $router->get('day/11[/part/{part}]', Day11Controller::class);
        $router->get('day/12[/part/{part}]', Day12Controller::class);
    });
});