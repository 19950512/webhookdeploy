# Configuração do Webhook para Deploy Automatizado

Este repositório contém instruções e arquivos necessários para configurar um webhook que permite o deploy automatizado de projetos hospedados no GitHub.
Siga os passos abaixo para configurar o webhook em seu servidor.

## Configurações Iniciais

#### Criação do arquivo *.env*

Na raiz do repositório, crie um arquivo chamado .env e insira o seguinte conteúdo:
```
SECRET_WEBHOOK_GITHUB=XXXXXXXXXXXX
PERSONAL_ACCESS_TOKEN_GITHUB=XXXXXXXXXXXXXXXXXX
USUARIO_GITHUB=XXXXX
DIRETORIO_DE_PROJETOS=/folder/my/projects
```

- **SECRET_WEBOOK_GITHUB**: É a 'secret' que você configura no momento que cria o webhook no github. ([Instruções](https://github.com/minhaConta/meuRepositorio/settings/hooks))
- **PERSONAL_ACCESS_TOKEN_GITHUB**: É o token de acesso pessoal que você pode gerar nas configurações da sua conta de desenvolvedor do GitHub. ([Obter Token](https://github.com/settings/tokens))
- **USUARIO_GITHUB**: Seu nome de usuário do GitHub. Por exemplo, se o seu perfil é `https://github.com/minhaConta`, o valor seria `minhaConta`
- **DIRETORIO_DE_PROJETOS**: Este é o diretório no servidor onde estão localizados os repositórios dos projetos. É a pasta principal que será usada para o deploy.

### Configuração do Webhook
Configure o VirtualHost (Apache) ou a maneira como o domínio do projeto é apontado para a pasta `~/src/Public`.