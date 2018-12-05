<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
$app->add(new Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

$app->add(new Slim\Middleware\Session([
  'name'        => 'default',
  'autorefresh' => true,
  'lifetime'    => '1 hour'
]));