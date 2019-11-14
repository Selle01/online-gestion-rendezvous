<?php
# php -S localhost:8007 -d display_errors=1 -t public/

use App\Controllers\AuthController;
use App\Controllers\MedecinController;
use App\Controllers\PatientController;
use App\Controllers\RendezVousController;
use App\Controllers\SecretaryController;
use App\Controllers\ServiceController;
use App\Controllers\SpecialtyController;
use App\Renderer\PHPRenderer;
use App\Session\PHPSession;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

require '../vendor/autoload.php';

$whoops = new \Whoops\Run;
$whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$builder = new \DI\ContainerBuilder();
$builder->build();
$renderer = new PHPRenderer(dirname(__DIR__) . '/views');
$app = new \App\App(
    [
        PHPSession::class,
        ServiceController::class,
        SpecialtyController::class,
        MedecinController::class,
        SecretaryController::class,
        PatientController::class,
        RendezVousController::class,
        AuthController::class,

    ],
    [
        'renderer' => $renderer,
    ]
);

$response = $app->run(ServerRequest::fromGlobals());
send($response);
