<?php

namespace App\Controller;

use App\Model\Product;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Home
{
    protected $view;
    protected $products;
    
    public function __construct(ContainerInterface $container)
    {
        $this->view     = $container->get('view');
        $this->products = $container->get(Product::class);
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $products = $this->products->get();

        $this->view->render($response, 'index.html', [
            'products' => $products,
        ]);
    }
}