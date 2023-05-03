<?php

namespace Codilar\Witch;

use Codilar\Witch\Checkers\Magento;
use Codilar\Witch\Checkers\Shopify;
use Codilar\Witch\Checkers\Cms;

class Checker
{
    /**
     * @param string $url
     * @param string $body
     * @return string
     */
    public function check($url, $body = '')
    {
        $url = trim($url, '/');

        $checkers = [
            new Magento(),
            new Shopify(),
            new Cms()
        ];
        $body = $this->makeCurlCall($url)['body'] ?? '';
        foreach ($checkers as $checker) {
            try {
                $result = @$checker->check($url, $body);
                if ($result) {
                    return $result;
                }
            } catch (\Exception $exception) {
                return 'ERROR';
            }
        }
        return '';
    }

    protected function makeCurlCall($url, $headers = [])
    {
        foreach ($headers as $key => $value) {
            $headers[$key] = $key . ': ' . $value;
        }
        $ch = curl_init();

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36';

        $curlConfig = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_TIMEOUT => 30
        ];

        curl_setopt_array($ch, $curlConfig);

        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'status' => intval($statusCode),
            'body' => $result
        ];
    }

}
