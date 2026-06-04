<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app = $app->make('Illuminate\Contracts\Foundation\Application');
$router = $app['router'];
$routes = $router->getRoutes();

echo "Total routes: " . count($routes) . PHP_EOL;
echo "\nRoutes containing 'review':" . PHP_EOL;

foreach ($routes as $route) {
  if (strpos($route->uri(), 'review') !== false) {
    $methods = implode(',', array_diff($route->methods(), ['HEAD']));
    echo $route->uri() . " | " . $methods . " | " . ($route->getName() ?? 'unnamed') . PHP_EOL;
    echo "  Action: " . ($route->getActionName() ?? 'unknown') . PHP_EOL;
  }
}
