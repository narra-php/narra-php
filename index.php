<?php

$autoload = require __DIR__ . '/vendor/autoload.php';

$app = new \NarraPhp\Application($autoload);

$app->run();