# WhatsApp Suite

Solução completa para gerenciamento WhatsApp com Docker.

## Componentes

| Componente | Descrição | Porta |
|------------|-----------|-------|
| **Manager** | Painel web Laravel 12 + Tailwind | 8080 |
| **API Go** | Backend Go com whatsmeow | 8084 |
| **PostgreSQL** | Database compartilhado | 5432 |

## Instalação com Docker

```bash
# 1. Clonar
git clone https://github.com/jadersistemas/whatsapp-suite.git
cd whatsapp-suite

# 2. Configurar
cp .env.docker .env
# Edite o .env com suas senhas

# 3. Subir tudo
docker compose up -d --build

# 4. Rodar migrations do Manager
docker compose exec manager php artisan migrate --force

# 5. Gerar APP_KEY
docker compose exec manager php artisan key:generate
```

Acesse: **http://localhost:8080**

## Instalação Manual (sem Docker)

### API Go

```bash
cd api
go build -o whatsapp-api ./cmd/api
./whatsapp-api
```

### Manager

```bash
cd manager
composer install
npm install && npm run build
cp .env.example .env
# Configure .env com banco PostgreSQL
php artisan key:generate
php artisan migrate
php artisan serve
```

## Variáveis de Ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_PORT` | Porta do Manager | `8080` |
| `API_PORT` | Porta da API Go | `8084` |
| `DB_PASSWORD` | Senha do PostgreSQL | `changeme` |
| `WHATSAPP_API_KEY` | Chave da API Go | `changeme` |
| `JWT_SECRET` | Segredo JWT | `change-this` |

## Deploy no VPS / EasyPanel

1. Copie a pasta `whatsapp-suite` para o servidor
2. Configure o `.env` com as senhas de produção
3. Execute `docker compose up -d --build`
4. Acesse pela porta 8080

### EasyPanel

Importe o `docker-compose.yml` pela interface do EasyPanel ou configure manualmente cada serviço.

## Estrutura

```
whatsapp-suite/
├── api/                    # WhatsApp API Go
│   ├── Dockerfile
│   ├── cmd/api/            # Entry point
│   ├── internal/           # Código Go
│   └── go.mod
├── manager/                # WhatsApp Manager (Laravel)
│   ├── Dockerfile
│   ├── nginx.conf
│   ├── app/                # Controllers, Models, Services
│   ├── resources/views/    # Templates Blade
│   └── routes/web.php
├── docker-compose.yml      # Compose completo
├── .env.docker             # Template de variáveis
└── README.md
```

## Licença

MIT
