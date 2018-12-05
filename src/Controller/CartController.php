<?php

namespace App\Controller;

use App\Model\Cart;
use App\Model\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Container\ContainerInterface;

class CartController
{
    protected $view; 
    protected $cart;
    protected $products;
    
    public function __construct(ContainerInterface $container)
    {
        $this->view     = $container->get('view');
        $this->cart     = $container->get('cart');
        $this->products = $container->get(Product::class);
        $this->router   = $container->get('router');
    }

    public function index(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'cart.html');
    }

    public function add(Request $request, Response $response, array $args)
    {
        $slug     = $args['slug'];
        $quantity = $args['quantity'];
        $product  = $this->products->where('slug', $slug)->first();

        if (!$product) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        try {
            $this->cart->add($product, $quantity);
        } catch (QuantityExceeded $e) {

        }

        return $response->withRedirect($this->router->pathFor('cart.index'));
    }
}