<?php
xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);

require_once(__DIR__ . '/bootstrap.php');
register_shutdown_function(array('Celd\ProfileGui\Profiler', 'profile'));
$app->get('/', 'Celd\ProfileGui\Controller\DefaultController::indexAction');
$app->get('/profile/{id}', 'Celd\ProfileGui\Controller\ProfileController::indexAction');
$app->get('/test', 'Celd\ProfileGui\Controller\DefaultController::testAction');

$app->get('/host/{host}', 'Celd\ProfileGui\Controller\FilterController::hostAction');


$app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views',
    ));

// Run application
$app->run();

