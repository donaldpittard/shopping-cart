<?php

namespace App\Test;

use App\Cart\Cart;
use App\Model\Product;
use App\Exception\QuantityExceeded as QuantityExceededException;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;
use SlimSession\Helper;

class CartTest extends TestCase
{
    public function testSuccessfulAdd()
    {
        $mockSession = $this->getMockBuilder(Helper::class)
            ->setMethods(['exists', 'get', 'set'])
            ->getMock();

        $mockSession->expects($sessionSpy = $this->any())
            ->method('set');

        $mockProduct = $this->getMockBuilder(Product::class)
            ->setMethods(['hasStock'])
            ->getMock();
        
        $mockProduct->expects($this->any())
            ->method('hasStock')
            ->willReturn(true);

        $mockProducts = $this->getMockBuilder(Product::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockProducts->expects($this->any())
            ->method('find')
            ->willReturn($mockProduct); 
            
        $cart = new Cart($mockSession, $mockProducts);

        $cart->add($mockProduct, 1);

        $invocations = $sessionSpy->getInvocations();

        $this->assertEquals(1, count($invocations));
    }

    public function testExceptionThrownWhenQuantityExceeded()
    {
        $mockSession = $this->createMock(Helper::class);

        $mockProduct = $this->getMockBuilder(Product::class)
            ->setMethods(['hasStock'])
            ->getMock();
        
        $mockProduct->expects($this->any())
            ->method('hasStock')
            ->willReturn(false);

        $mockProducts = $this->getMockBuilder(Product::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockProducts->expects($this->any())
            ->method('find')
            ->willReturn($mockProduct); 
            
        $cart = new Cart($mockSession, $mockProducts);

        $this->expectException(QuantityExceededException::class);
        $cart->add($mockProduct, 1);
    }
}