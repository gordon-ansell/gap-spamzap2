<?php
/**
 * This file is part of the GreenFedora PHP framework, generator package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

// Bring in what we need.
use Gf\Gf;
use Gf\Kernel;
use GreenFedora\Container\Container;

// Get the base path.
$basePath = dirname(__DIR__);

// Create the application.
$app = new Gf($basePath, __DIR__);

// Register the application instance.
$app->registerSingletonInstance('app', $app);
Container::setInstance($app);

// Get the environment variables and load the configs.
$app->loadConfigs($basePath);

// Get a logger going.
$app->configureStandardLogger();

// Register the kernel.
$app->registerSingleton('kernel', Kernel::class);

// We've booted, so we can now do application-specific initialisation.
$app->init();

// Return the app.
return $app;
