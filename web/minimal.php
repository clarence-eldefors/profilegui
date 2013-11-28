<?php
xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);

defined('APPLICATION_PATH') ||
define('APPLICATION_PATH', realpath(dirname(__FILE__)));


require_once APPLICATION_PATH . '/../vendor/autoload.php';

register_shutdown_function(array('Celd\ProfileGui\Profiler', 'profile'));

