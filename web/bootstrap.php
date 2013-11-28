<?php
// Path to application
defined('APPLICATION_PATH') ||
define('APPLICATION_PATH', realpath(dirname(__FILE__)));


require_once APPLICATION_PATH . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;