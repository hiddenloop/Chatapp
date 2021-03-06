<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  2.1.0
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Tick... Tock... Tick... Tock...
// --------------------------------------------------------------
define('LARAVEL_START', microtime(true));
define('DOCROOT', __DIR__);
define('APPROOT', "/var/www/Chatapp");
define('LIBRARIES', DOCROOT.'/../application/libraries');
define ('HYBRID_CONFIG', APPROOT."/public/hybridauth/config.php");
define ('PUSHERKEY', '');
define ('PUSHERSECRET', '');
define ('PUSHERAPPID', '');
// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
$application = '../application';

// --------------------------------------------------------------
// The path to the Laravel directory.
// --------------------------------------------------------------
$laravel = '../laravel';

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
$public = __DIR__;

// --------------------------------------------------------------
// Launch Laravel.
// --------------------------------------------------------------
require $laravel.'/laravel.php';