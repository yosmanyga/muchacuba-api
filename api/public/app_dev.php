<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symsonte\Http\App;
use Symsonte\ServiceKit\CachedContainer;
use Symfony\Component\Debug\Debug;

Debug::enable();

$app = new App(new CachedContainer(
    sprintf('%s/../config/parameters.yml', __DIR__),
    [],
    sprintf('%s/../var/cache', __DIR__),
    ['Cubalider', 'Muchacuba', 'Symsonte']
));
$app->execute('muchacuba.http.server.controller_dispatcher');
