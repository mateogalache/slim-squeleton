<?php
declare(strict_types=1);

use SallePW\SlimApp\Controller\ChangePasswordController;
use SallePW\SlimApp\Controller\CookieMonsterController;
use SallePW\SlimApp\Controller\CreateUserController;
use SallePW\SlimApp\Controller\FileController;
use SallePW\SlimApp\Controller\FlashController;
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\HomePageController;
use SallePW\SlimApp\Controller\MarketController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\SignInController;
use SallePW\SlimApp\Controller\SignUpController;
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

$app->get(
    '/sign-up',
    SignUpController::class . ':showSignUp'
);

$app->post(
    '/sign-up',
    SignUpController::class . ':uploadSignUp'
);

$app->get(
    '/sign-in',
    SignInController::class . ':showSignIn'
)->setName('sign-in');

$app->post(
    '/sign-in',
    SignInController::class . ':uploadSignIn'
);

$app->get(
    '/home',
    HomePageController::class . ':showHomePage'
);

$app->get(
    '/market',
    MarketController::class . ':showMarket'
);

$app->get(
    '/changepassword',
    ChangePasswordController::class . ':showChangePassword'
);

$app->get(
    '/profile',
    ProfileController::class . ':showProfile'
);