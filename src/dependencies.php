<?php

use App\Action\InvoiceAction;
use App\Action\LeadAction;
use App\Action\PaymentAction;
use Interop\Container\ContainerInterface;
use Slim\Csrf\Guard;
use Web\Component\Responder;
use Web\Middleware\CsrfForm;

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

$container['csrf'] = function ($c) {
    return new Guard();
};

$container['csrfForm'] = function (ContainerInterface $container) {
    return new CsrfForm($container->get('csrf'));
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
$container[LeadAction::class] = function ($c) {
    return new LeadAction($c->get('responder'), $c->get('rebilly'));
};

$container[PaymentAction::class] = function ($c) {
    return new PaymentAction($c->get('responder'), $c->get('rebilly'), $c->get('settings')['templatePath']);
};

$container[InvoiceAction::class] = function ($c) {
    return new InvoiceAction($c->get('responder'), $c->get('rebilly'));
};
