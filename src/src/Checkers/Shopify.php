<?php


namespace Codilar\Witch\Checkers;


use Codilar\Witch\Checker;

class Shopify extends Checker
{
    public function check($url, $body = '')
    {
        $token = 'var Shopify = Shopify || {};';
        if (strpos($body, $token) !== false) {
            return 'Shopify';
        }
        return null;
    }
}
