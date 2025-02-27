# Nome da aplicação (utilizado nos containers Docker)
PROJECT_NAME=book-management-app

# Helper para exibir todos os comandos disponíveis
help:
	@echo "====================== MAKEFILE COMMANDS ======================"
	@echo "Uso: make <comando>"
	@echo ""
	@echo "Comandos disponíveis:"
	@echo "up                - Sobe os containers Docker em segundo plano"
	@echo "down              - Para e remove os containers"
	@echo "restart           - Reinicia todos os serviços Docker"
	@echo "build             - Constrói as imagens Docker"
	@echo "build-up          - Constrói as imagens e sobe os containers em segundo plano"
	@echo "logs              - Exibe os logs dos containers"
	@echo "bash              - Acessa o shell do container PHP"
	@echo "composer-install  - Instala dependências com o Composer"
	@echo "composer-update   - Atualiza dependências com o Composer"
	@echo "cache-clear       - Limpa o cache da aplicação Symfony"
	@echo "cache-warmup      - Reaquece o cache da aplicação Symfony"
	@echo "migrate           - Executa migrações do banco de dados com Doctrine"
	@echo "migration-generate - Cria uma nova classe de migração com Doctrine"
	@echo "run-tests         - Executa os testes com PHPUnit"
	@echo "db-shell          - Acessa o shell do banco de dados MySQL"
	@echo "clean             - Remove containers e volumes do Docker"
	@echo "help              - Exibe esta ajuda"
	@echo "==============================================================="

# Comandos básicos
up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down && docker compose up -d

build:
	docker compose build

# Novo comando: Build e UP juntos
build-up:
	docker compose build && docker compose up -d

# Logs dos containers
logs:
	docker compose logs -f

# Acessar o shell do container da aplicação
bash:
	docker exec -it book_api bash

# Composer dentro do container
composer-install:
	docker compose exec php composer install

composer-update:
	docker compose exec php composer update

# Cache do Symfony
cache-clear:
	docker compose exec php php bin/console cache:clear

cache-warmup:
	docker compose exec php php bin/console cache:warmup

# Migrações do Doctrine
migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

migration-generate:
	docker compose exec php php bin/console doctrine:migrations:generate

# Testes
run-tests:
	@docker ps --filter "name=book_api" --filter "status=running" | grep "book_api" > /dev/null || (echo "Container 'book_api' is not running. Starting containers..."; docker compose up -d)
	docker exec -it book_api ./vendor/bin/phpunit --configuration phpunit.xml.dist



run-tests-coverage:
	docker compose exec php ./vendor/bin/phpunit --coverage-html var/coverage

# Banco de dados
db-shell:
	docker compose exec db mysql -u user -ppassword book_db

# Remover volumes e containers
clean:
	docker compose down -v
