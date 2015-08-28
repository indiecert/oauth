<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\IndieCert\OAuth\Service;
use fkooman\Ini\IniReader;
use fkooman\OAuth\OAuthServer;
use fkooman\OAuth\Storage\PdoCodeTokenStorage;
use fkooman\OAuth\Storage\JsonResourceServerStorage;
use fkooman\OAuth\Storage\UnregisteredClientStorage;
use fkooman\Rest\Plugin\Authentication\IndieAuth\IndieAuthAuthentication;
use fkooman\Tpl\Twig\TwigTemplateManager;

// CONFIG
$iniReader = IniReader::fromFile(
    dirname(__DIR__).'/config/server.ini'
);

// USER AUTH
$userAuthentication = new IndieAuthAuthentication();
$userAuthentication->setUnauthorizedRedirectUri('/identify');

// DB
$db = new PDO(
    $iniReader->v('Db', 'dsn'),
    $iniReader->v('Db', 'username', false),
    $iniReader->v('Db', 'password', false)
);
$pdoCodeTokenStorage = new PdoCodeTokenStorage($db);

// TEMPLATE MANAGER
$templateManager = new TwigTemplateManager(
    array(
        dirname(__DIR__).'/views',
        dirname(__DIR__).'/config/views',
    )
);

// SERVER
$server = new OAuthServer(
    $templateManager,
    new UnregisteredClientStorage(),
    new JsonResourceServerStorage(dirname(__DIR__).'/config/resource_servers.json'),
    $pdoCodeTokenStorage,
    $pdoCodeTokenStorage
);

$service = new Service($server, $userAuthentication, $templateManager);
$service->run()->send();
