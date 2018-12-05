<?php
namespace App\Exception;

use Exception;

class QuantityExceeded extends Exception
{
    protected $message = 'You have added the maxiumum stock for this item.';
}