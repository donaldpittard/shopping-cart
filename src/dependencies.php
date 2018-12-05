<?php
// DIC configuration
$baseDir     = realpath(__DIR__ . '/..');
$templateDir = $baseDir . '/templates';
$cacheDir    = $baseDir . '/cache';
$container   = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// views
$container['view'] = function ($c) use ($templateDir, $cacheDir) {
    $view   = new Slim\Views\Twig($templateDir, ['cache' => false]);
    $router = $c->get('router');
    $uri    = Slim\Http\Uri::createFromEnvironment(new Slim\Http\Environment($_SERVER));

    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));
    $view->getEnvironment()->addGlobal('cart', $c->get('cart'));

    return $view;
};

// database
$container['db'] = function ($c) {
    $dbSettings = $c->get('settings')['db'];
    $capsule    = new Illuminate\Database\Capsule\Manager;

    $capsule->addConnection($dbSettings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

// models
$container[App\Model\Product::class] = function ($c) {
    return new App\Model\Product;
};

// session
$container['session'] = function ($c) {
    return new SlimSession\Helper;
};

$container['cart'] = function ($c) {
    $session  = $c->get('session');
    $products = $c->get(App\Model\Product::class);

    return new App\Cart\Cart($session, $products);
};

