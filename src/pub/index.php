<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/media.php';

$templatePath = __DIR__ . '/src/templates';
$layout = __DIR__ . '/src/layout/main.phtml';

$mediaDir = __DIR__ . '/media';

$router = new \Codilar\Witch\Router();

$router->setData('appName', 'WITCH&trade;');

$router->addRoute('/', 'GET', $templatePath . '/home.phtml', $layout);
$router->addRoute('404', 'GET', $templatePath . '/404.phtml', $layout);

$router->addRoute('/media/(.*)', 'GET', new Media($mediaDir), $layout);

$router->addRoute('/single-url', 'GET', $templatePath . '/single-url.phtml', $layout);
$router->addRoute('/single-url', 'POST', $templatePath . '/single-url.phtml', $layout);

$router->addRoute('/csv', 'GET', $templatePath . '/csv.phtml', $layout);
$router->addRoute('/csv', 'POST', $templatePath . '/csv.phtml', $layout);

$router->route();
