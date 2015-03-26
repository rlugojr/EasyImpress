<?php
/*
* This file is part of the Orbitale EasyImpress package.
*
* (c) Alexandre Rock Ancelet <alex@orbitale.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

define('APPDIR',    __DIR__.'/../src/');
define('WEBDIR',    __DIR__.'/../web/');
define('VIEWSDIR',  __DIR__.'/../views/');
define('JSDIR',     __DIR__.'/../web/js/');
define('CSSDIR',    __DIR__.'/../web/css/');
define('TRANSDIR',  __DIR__.'/../translations/');
define('SLIDESDIR', __DIR__.'/../slides/');

if (!file_exists(__DIR__.'/config.php')) {
    echo 'You must define app constants in the config directory. Take example with "constants.php.dist" to know what you need.';
    exit;
}
require 'config.php';
