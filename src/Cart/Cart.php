<?php

namespace App\Cart;

use App\Exception\QuantityExceeded as QuantityExceededException;
use App\Model\Product;
use SlimSession\Helper as Storage;

class Cart
{
    protected $session;
    protected $products;

    public function __construct(Storage $session, Product $products)
    {
        $this->session  = $session;
        $this->products = $products;
    }

    public function add(Product $product, int $quantity)
    {
        if ($this->has($product)) {
            $quantity = $this->get($product)['quantity'] + $quantity;
        }

        $this->update($product, $quantity);
    }

    public function update(Product $product, int $quantity)
    {
        $hasStock = $this->products
            ->find($product->id)
            ->hasStock($quantity);

        if (!$hasStock) {
            throw new QuantityExceededException;
        }

        if ($quantity === 0) {
            $this->remove($product);
            return;
        }

        $this->session->set($product->id, [
            'product_id' => $product->id,
            'quantity'   => $quantity,
        ]);
    }

    public function remove(Product $product)
    {
        $this->session->delete($product->id);
    }

    public function has(Product $product)
    {
        return $this->session->exists($product->id);
    }

    public function get(Product $product)
    {
        return $this->session->get($product->id);
    }

    public function clear()
    {
        $this->session->clear();
    }

    public function all()
    {
        $it    = $this->session->getIterator();
        $ids   = [];
        $items = [];

        foreach ($it as $product) {
            $ids[] = $product['product_id'];
        }

        $products = $this->products->find($ids);

        foreach ($products as $product) {
            $product->quantity = $this->get($product)['quantity'];
            $items[] = $product;
        }

        return $items;
    }

    public function itemCount(): int
    {
        return count($this->session);
    }
}