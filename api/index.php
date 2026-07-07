<?php
/**
 * Vercel Serverless Function Entry Point for Laravel
 * 
 * This file handles all requests for the Laravel application
 * on Vercel's serverless PHP environment.
 */

// Define the base path for the Laravel application
$basePath = dirname(dirname(__FILE__));

// Set the public path for Vite assets
define('LARAVEL_START', microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Composer's autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap the Laravel application
/** @var \Illuminate\Foundation\Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// Handle the request through Laravel's HTTP kernel
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
