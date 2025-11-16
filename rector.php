<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Command',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Handler',
        __DIR__ . '/GWKDynamoSessionBundle.php',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_74,
        SymfonySetList::SYMFONY_44,
        SymfonySetList::SYMFONY_CODE_QUALITY
    ]);
