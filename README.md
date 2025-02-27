# Book Management API
Uma aplicação para o gerenciamento de livros, construída com Symfony e Doctrine ORM. Este projeto utiliza Docker para a configuração do ambiente, além de um `Makefile` para simplificar tarefas comuns durante o desenvolvimento. Ele também suporta geração de relatórios em PDF utilizando o **KNP Snappy Bundle**.
## Recursos
- **PHP 8.3**: Versão mais recente do PHP, com maior segurança e desempenho.
- **Symfony 7.2**: Framework moderno e robusto para desenvolvimento eficiente.
- **Doctrine ORM**: ORM confiável para manipulação de dados relacionais.
- **MySQL**: Banco de dados principal utilizado pela aplicação.
- **KNP Snappy Bundle**: Geração de relatórios em PDF (utilizando o wkhtmltopdf).
- **Nelmio Cors Bundle**: Configurações ajustadas para CORS.
- **Portainer**: Serviço opcional para gerenciamento de contêineres Docker.
- **Docker e Docker Compose**: Contêineres para isolar os serviços e facilitar o setup do ambiente.
- **Makefile**: Lista de comandos automatizados para tarefas como build, configurações e execução de testes.

## Pré-requisitos
Certifique-se de que sua máquina possui os seguintes softwares instalados:
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [GNU Make](https://www.gnu.org/software/make/)
- [wkhtmltopdf](https://wkhtmltopdf.org/) (é necessário para gerar PDFs com o KNP Snappy Bundle).


> **Observação:** No ambiente Docker, o wkhtmltopdf já estará configurado para uso dentro do contêiner.
>

## Configuração de Variáveis de Ambiente
Antes de iniciar o projeto, configure o arquivo `.env` com as variáveis de ambiente necessárias. Um exemplo base está disponível no arquivo `.env.example`, e você pode copiá-lo para criar seu arquivo local:
``` bash
    cp .env.example .env
```
Certifique-se de incluir e ajustar as variáveis abaixo no arquivo `.env`:
``` dotenv
    ### Configuração do Banco de Dados
    DATABASE_URL="mysql://user:password@db:3306/book_db"
    MYSQL_ROOT_PASSWORD=password
    MYSQL_DATABASE=book_db
    MYSQL_USER=user
    MYSQL_PASSWORD=password
    ###< doctrine/doctrine-bundle ###
    
    ### Configuração JWT
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    
    ### Configuração do Redis
    REDIS_HOST=redis://localhost
    
    ### Configuração do Portainer (opcional)
    PORTAINER_WEB_PORT=9000:9000
    PORTAINER_DNS_DOMAIN=
    PORTAINER_VERSION=latest
    
    ### Configurações CORS (Nelmio Cors Bundle)
    CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
    
    ### Configuração do KNP Snappy Bundle
    WKHTMLTOPDF_PATH=/usr/bin/wkhtmltopdf
    WKHTMLTOIMAGE_PATH=/usr/bin/wkhtmltoimage
```
## Instalação e Configuração
Siga estas etapas para configurar e executar a aplicação.
### 1. Clone o Repositório
``` bash
    git clone https://github.com/seu-usuario/book-management-api.git
    cd book-management-api
```
### 2. Configure as Variáveis de Ambiente
Siga as instruções acima para configurar e ajustar o arquivo `.env`.
### 3. Build e Suba os Contêineres
Para construir as imagens Docker e iniciar todos os serviços necessários em segundo plano, utilize:
``` bash
    make build-up
```
Este comando irá:
- Construir as imagens Docker.
- Subir os contêineres definidos no `docker-compose.yml`.

### 4. Crie o Banco de Dados
Após iniciar o ambiente, crie o banco de dados executando o comando abaixo dentro do contêiner:
``` bash
    make bash
    php bin/console doctrine:database:create
```
### 5. Execute as Migrações
Com o banco de dados criado, aplique as migrações para criar as tabelas e a estrutura necessária:
``` bash
    php bin/console doctrine:migrations:migrate --no-interaction
```
## Gerando Relatórios em PDF
O projeto utiliza o **KNP Snappy Bundle** para geração de relatórios em PDF. Relatórios podem ser gerados de maneira simples dentro da aplicação, com suporte a layouts de alta qualidade renderizados em HTML.
Para garantir o funcionamento, verifique se o caminho configurado para o wkhtmltopdf no `.env` é válido:
``` env
    WKHTMLTOPDF_PATH=/usr/bin/wkhtmltopdf
    WKHTMLTOIMAGE_PATH=/usr/bin/wkhtmltoimage
```
No ambiente de desenvolvimento, o contêiner já inclui o binário do wkhtmltopdf configurado no caminho padrão.
## Utilizando o Makefile
O `Makefile` foi preparado para simplificar tarefas comuns no desenvolvimento. Abaixo está uma lista dos comandos mais importantes:

    | Comando | Descrição |
    | --- | --- |
    | `make build-up` | Constrói imagens Docker e sobe os contêineres. |
    | `make up` | Sobe os contêineres em segundo plano. |
    | `make down` | Para e remove contêineres e serviços. |
    | `make restart` | Reinicia os serviços Docker. |
    | `make logs` | Exibe os logs dos contêineres. |
    | `make bash` | Acessa o shell do contêiner PHP. |
    | `make composer-install` | Instala as dependências PHP com Composer. |
    | `make migrate` | Executa as migrações do banco de dados com Doctrine. |
    | `make run-tests` | Executa os testes com PHPUnit. |
    | `make clean` | Remove volumes, imagens e contêineres do Docker. |
Para ver a lista completa de comandos disponíveis, execute:
``` bash
    make help
```
## Estrutura do Projeto
Abaixo está um resumo das principais pastas do projeto:
- **src/**: Código-fonte principal da aplicação.
    - **Entity/**: Classes que representam as entidades no banco de dados.
    - **Controller/**: Controladores que lidam com as solicitações HTTP.
    - **Repository/**: Repositórios personalizados para integração com o banco de dados.
    - **Service/**: Classes de serviços responsáveis por regras de negócio e lógica reutilizável.
    - **Exception/**: Exceções personalizadas para tratar erros específicos da aplicação.

- **migrations/**: Arquivos de migração do banco gerados pelo Doctrine.
- **config/**: Arquivos de configuração do Symfony.
- **tests/**: Testes unitários e de integração para o projeto.
- **docker-compose.yml**: Configuração de serviços Docker.
- **Makefile**: Automação de tarefas para o desenvolvimento.

## Contribua para o Projeto
Contribuições para o projeto são sempre bem-vindas! Siga as etapas abaixo para contribuir:
1. Faça um fork do repositório.
2. Crie uma branch para sua funcionalidade ou correção:
``` bash
   git checkout -b minha-feature
```
1. Faça suas alterações, criando commits claros e explicativos.
2. Envie suas alterações para o repositório remoto:
``` bash
   git push origin minha-feature
```
1. Abra uma **Pull Request** no repositório original.
