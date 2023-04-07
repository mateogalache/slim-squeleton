<?php
declare(strict_types=1);

use SallePW\SlimApp\Controller\CookieMonsterController;
use SallePW\SlimApp\Controller\CreateUserController;
use SallePW\SlimApp\Controller\FileController;
use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\VisitsController;
use SallePW\SlimApp\Middleware\beforeMiddleware;
use SallePW\SlimApp\Middleware\StartSessionMiddleware;


$app->add(StartSessionMiddleware::class);

$app->get(
    '/',
    HomeController::class . ':apply')
    ->setName('home');
$app->get(
    '/visits',
    VisitsController::class . ":showVisits"
)->setName('visits');

$app->get(
    '/cookies',
    CookieMonsterController::class . ":showAdvice"
)->setName('cookies');

$app->get(
    '/flash',
    FlashController::class . ":addMessage"
)->setName('flash');

$app->post(
    '/user',
    CreateUserController::class . ":apply"
)->setName('create_user');

$app->get(
    '/files',
    FileController::class . ':showFileFormAction'
);

$app->post(
    '/files',
    FileController::class . ':uploadFileAction'
)->setName('upload');