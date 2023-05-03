<?php


namespace Codilar\Witch\Checkers;


use Codilar\Witch\Checker;
use PHPHtmlParser\Dom;

class Cms extends Checker
{
    public function check($url, $body = '')
    {
        try {
            $dom = new Dom();
            $dom->loadStr($body);
            /** @var \PHPHtmlParser\Dom\Node\HtmlNode $meta */
            $meta = $dom->find('meta[name="generator"]')->toArray()[0] ?? null;
            if ($meta) {
                $tech = $meta->getAttribute('content');
                if ($tech) {
                    return $tech;
                }
            }
        } catch (\Exception $exception) {}
        return null;
    }
}
