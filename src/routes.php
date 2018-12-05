<?php

use App\Controller\{Home, ProductController, CartController};

$app->get('/', Home::class)->setName('home');
$app->get('/products/{slug}', ProductController::class . ':get')->setName('product.get');
$app->get('/cart', CartController::class . ':index')->setName('cart.index');
$app->get('/cart/add/{slug}/{quantity}', CartController::class . ':add')->setName('cart.add');