<?php
/*
Plugin Name: SpamZap2
Plugin URI: http://gordonansell.com/spamzap2/
Description: Prevent chosen emails, domains and IP addresses from registering with or commenting on a site.
Author: Gordon Ansell
Version: 1.0.0.dev.19
Author URI: http://gordonansell.com
*/
declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Bring in the autoloader.
require __DIR__ . '/vendor/autoload.php';

// Create the application.
$pluginFile = __FILE__;
$app = require __DIR__ . '/app/boot.php';

// Create the kernel.
$kernel = $app->get('kernel');

// ===================
// Userland.
// ===================


// ===================

if ( ! session_id() ) {
    session_start();
}

use App\UserMode;
use App\AdminMode;
use App\Update;

// Dispatch to the kernel.
if (\is_admin()) {
    $app->configureTemplateSystem();
    $kernel->dispatch(new UserMode($app), new AdminMode($app), new Update($app));
} else {
    $kernel->dispatch(new UserMode($app), null);
}


