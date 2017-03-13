<?php

use App\Action\AccountAction;
use App\Action\PaymentAction;
use App\Action\UpsellAction;
use Interop\Container\ContainerInterface;
use Web\Component\Responder;

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    return new Slim\Views\PhpRenderer(__DIR__ . '/../templates/');
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));

    return $logger;
};

$container['responder'] = function (ContainerInterface $container) {
    return new Responder($container->get('renderer'));
};

$container['rebilly'] = function ($c) {
    return new Rebilly\Client(
        [
            'apiKey' => $c->get('settings')['rebilly_api']['api_key'],
            'baseUrl' => $c->get('settings')['rebilly_api']['api_host'],
        ]
    );
};

// actions:
$container[PaymentAction::class] = function ($c) {
    return new PaymentAction($c->get('responder'), $c->get('rebilly'));
};

$container[AccountAction::class] = function ($c) {
    return new AccountAction($c->get('responder'), $c->get('rebilly'), $c->get('settings')['baseUrl']);
};

$container[UpsellAction::class] = function ($c) {
    return new UpsellAction($c->get('responder'), $c->get('rebilly'));
};
