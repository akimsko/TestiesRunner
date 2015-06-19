<?php
require_once __DIR__ . 'autoloaderloader.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->addCommands(array(
    new TestiesRunner\Command\Run()
));

$application->run();
