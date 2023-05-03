<?php

namespace Codilar\Witch\Checkers;

use Codilar\Witch\Checker;
use PHPHtmlParser\Dom;

class Magento extends Checker
{

    public function check($url, $body = '')
    {
        $result = $this->checkMagentoVersion($url);
        if (!$result) {
            $result = $this->checkRestApiEndpoint($url);
        }
        if (!$result) {
            $dom = new Dom();
            $dom->loadStr($body);
            $result = $this->checkXMagentoInit($dom);
            if (!$result) {
                $result = $this->checkBodyClass($dom);
            }
        }
        return $result;
    }

    protected function checkXMagentoInit($dom)
    {
        if ($dom->find('script[type="text/x-magento-init"]')->count()) {
            return 'Magento 2';
        }
        return null;
    }

    protected function checkBodyClass($dom)
    {
        if ($dom->find('body.cms-index-index')->count()) {
            return 'Magento';
        }
        return null;
    }

    protected function checkMagentoVersion($url)
    {
        $url = $url . '/magento_version';
        $response = $this->makeCurlCall($url);
        if ($response['status'] === 200) {
            if (strlen($response['body']) < 100) {
                return $response['body'];
            }
        }
        return null;
    }

    protected function checkRestApiEndpoint($url)
    {
        $url = $url . '/rest/V1/products';
        $response = $this->makeCurlCall($url, ['Content-Type' => 'application/json']);
        if ($response['status'] === 401) {
            $body = \json_decode($response['body'], true);
            if ($body) {
                if (isset($body['message']) && $body['message'] === 'The consumer isn\'t authorized to access %resources.') {
                    return 'Magento 2';
                }
            }
        }
        return null;
    }
}
