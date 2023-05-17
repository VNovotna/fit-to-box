<?php

use App\Client\PackagingRequestFactory;
use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$config = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
$config->setNamingStrategy(new UnderscoreNamingStrategy());

return new Container([
    EntityManager::class => function (Container $c) {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        return EntityManager::create([
            'driver' => 'pdo_mysql',
            'host' => 'shipmonk-packing-mysql',
            'user' => 'root',
            'password' => 'secret',
            'dbname' => 'packing',
        ], $config);
    },
    Client::class => function (Container $c) {
        return new Client([
            'base_uri' => 'https://global-api.3dbinpacking.com/',
            'timeout' => 8.0,
        ]);
    },
    PackagingRequestFactory::class => function (Container $c) {
        return new PackagingRequestFactory('viktorie@novotna.cf', 'abcceb45cb3d4df9598fca7b62d7ef6e');
    },
    PackagingApi::class => \DI\autowire(PackagingApi::class),
    Application::class => \DI\autowire(Application::class),
]);