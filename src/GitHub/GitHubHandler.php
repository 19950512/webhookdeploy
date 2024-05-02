<?php

declare(strict_types=1);

namespace App\GitHub;

use App\Utils\Env;
use App\Utils\Logger;
use Exception;

class GitHubHandler
{
    private array $payload;
    private string $userGitHub;
    private string $accessToken;

    public string $response;

    public function __construct(
        private Env $env,
        private Logger $logger,
        private array $headers,
        private string $dataPost
    ){

        $this->validacaoRequest($this->dataPost);

        $json = match($this->headers['CONTENT_TYPE']){
            'application/x-www-form-urlencoded' => $_POST['payload'],
            'application/json' => $this->dataPost,
            default => throw new Exception('Não suportado o content type: "' . $this->headers['CONTENT_TYPE'] . '"'),
        };

        $this->payload = json_decode($json, true);

        $this->userGitHub = $this->env->get('USUARIO_GITHUB');
        $this->accessToken = $this->env->get('PERSONAL_ACCESS_TOKEN_GITHUB');
    }

    private function validacaoRequest($dataPost): void
    {

        if (!isset($this->headers['HTTP_X_HUB_SIGNATURE'])) {
            throw new Exception("Está faltando X-Hub-Signature no cabeçalho.");
        }elseif (!extension_loaded('hash')) {
            throw new Exception("Está faltando a extenção Hash.");
        }

        list($algo, $hash) = explode('=', $this->headers['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
        if (!in_array($algo, hash_algos(), true)) {
            throw new Exception("O algoritimo de hash '$algo' não é suportado.");
        }

        if (!hash_equals($hash, hash_hmac($algo, $dataPost, $this->env->get('SECRET_WEBHOOK_GITHUB')))) {
            throw new Exception('O segredo do Hook não foi reconhecido.');
        }

        if (!isset($this->headers['CONTENT_TYPE'])) {
            throw new Exception("Está faltando o Content-Type no cabeçalho.");
        } elseif (!isset($this->headers['HTTP_X_GITHUB_EVENT'])) {
            throw new Exception("Está faltando o 'X-Github-Event' no cabeçalho.");
        }
    }

    public function execute(): void
    {
        $this->response = match(mb_strtolower($this->headers['HTTP_X_GITHUB_EVENT'])){
            'ping' => 'pong',
            'push' => $this->push(),
            default => "Event: {$this->headers['HTTP_X_GITHUB_EVENT']} Payload: {$this->payload}"
        };
    }

    private function push(): string
    {
        $repositorio = $this->payload['repository']['name'];

        $path_repositorio = $this->env->get('DIRETORIO_DE_PROJETOS').'/'.$repositorio;

        if(!is_dir($path_repositorio)){
            $this->logger->log("O repositório {$repositorio} não encontrado no diretório dos projetos {$this->env->get('DIRETORIO_DE_PROJETOS')}.");
            throw new Exception('Repositório não reconhecido.');
        }

        $commands = array(
            "cd $path_repositorio && git restore ./",
            "cd $path_repositorio && git pull https://{$this->userGitHub}:{$this->accessToken}@github.com/{$this->userGitHub}/$repositorio.git",
        );

        foreach($commands as $command){
            $tmp = shell_exec("$command 2>&1");
            $tmp = is_string($tmp) ? $tmp : '';

            $this->logger->log($tmp);
        }

        return 'ok';
    }
}