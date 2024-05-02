<?php

declare(strict_types=1);

namespace App\Utils;

use Exception;

final class Env
{

    private static array $values = [];
    private static array $constants_necessary = [
        'SECRET_WEBHOOK_GITHUB', 'PERSONAL_ACCESS_TOKEN_GITHUB', 'USUARIO_GITHUB', 'DIRETORIO_DE_PROJETOS'
    ];

    public function __construct()
    {
        self::load();
    }

    private static function load(): void
    {
        $pathEnvFile = __DIR__.'/../../.env';
        if(!is_file($pathEnvFile)) {
            throw new Exception('Env file não encontrado.');
        }
        $envoriment = file_get_contents($pathEnvFile);
        $envoriment = explode("\n", $envoriment);

        foreach($envoriment as $env){
            $env = explode('=', $env);
            if(count($env) == 2){
                self::$values[$env[0]] = $env[1];
            }
        }

        foreach(self::$constants_necessary as $constant){
            if(!isset(self::$values[$constant])){
                throw new Exception('É necessário configurar a constante "'.$constant.'"');
            }
        }
    }

    static function get(string $keyName): string
    {

        if(count(self::$values) <= 0){
            self::load();
        }

        return self::$values[$keyName] ?? throw new Exception('A constante '.$keyName.' não existe.');
    }
}