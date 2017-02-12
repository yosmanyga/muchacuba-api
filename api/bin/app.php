<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Debug\Debug;
use Symsonte\Cli\App;
use Symsonte\ServiceKit\CachedContainer;

Debug::enable();

$app = new App(new CachedContainer(
    sprintf('%s/../config/parameters.yml', __DIR__),
    [],
    sprintf('%s/../var/cache', __DIR__),
    ['Cubalider', 'Muchacuba', 'Symsonte']
));
$app->execute('muchacuba.cli.server.command_dispatcher');
