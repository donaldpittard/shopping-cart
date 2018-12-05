<?php

namespace App\Controller;

use App\Model\Product;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProductController
{
    protected $view;
    protected $products;
    protected $router;
    
    public function __construct(ContainerInterface $container)
    {
        $this->view     = $container->get('view');
        $this->products = $container->get(Product::class);
        $this->router   = $container->get('router');
    }

    public function get(Request $request, Response $response, array $args)
    {
        $view    = $this->view;
        $slug    = $args['slug'];
        $product = $this->products->where('slug', $slug)->first();

        if (!$product) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $view->render($response, 'product.html', [
            'product' => $product,
        ]);
    }
}