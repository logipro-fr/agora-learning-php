<?php

declare(strict_types=1);

$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->addPsr4('AgoraLearningPhp\\Tests\\', __DIR__ . '/unit');
$loader->addPsr4('AgoraLearningPhp\\', __DIR__ . '/../src');
