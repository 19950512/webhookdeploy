<?php

declare(strict_types=1);

namespace App\Public;

$pathVendor = __DIR__.'/../../vendor/autoload.php';
$pathLoader = __DIR__.'/../Utils/loader.php';
if(!file_exists($pathLoader)){
    die('Loader não encontrado.');
}
require_once $pathLoader;

if (!file_exists($pathVendor)) {
    die('Execute o comando: composer install');
}

require_once $pathVendor;

use App\GitHub\GitHubHandler;
use App\Utils\Env;
use App\Utils\Logger;
use Exception;

$env = new Env();
$logger = new Logger();

try {

    $githubHandler = new GithubHandler(
        env: $env,
        logger: $logger,
        headers: $_SERVER,
        dataPost: file_get_contents('php://input')
    );

    $githubHandler->execute();

    echo $githubHandler->response;
    exit;

}catch (Exception $erro){

    $logger->log($erro->getMessage());

    header('HTTP/1.1 500 Internal Server Error');
    echo $erro->getMessage();
    exit;
}

