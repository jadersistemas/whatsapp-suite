# WhatsApp Manager

Painel de gerenciamento WhatsApp construído com Laravel 12, integrado à [WhatsApp API Go](https://github.com/nicehash/nicehash-whatsmeow-api).

## Funcionalidades

- **Dashboard** com status das instâncias
- **Criar/Remover instâncias** WhatsApp
- **Conexão via QR Code** ou Código de Pareamento
- **Envio de mensagens:** texto, link com preview, mídia, contato, localização, reação
- **Verificação de números** no WhatsApp
- **Configuração de Webhooks**
- **Upload de mídia** direto do computador

## Stack

- **Backend:** Laravel 12 + PHP 8.2
- **Frontend:** Vite + Tailwind CSS 4
- **Database:** PostgreSQL 17
- **API:** WhatsApp API Go (whatsmeow)
- **Container:** Docker + Nginx

## Requisitos

- PHP 8.2+
- Composer
- Node.js 20+
- PostgreSQL 17+
- WhatsApp API Go rodando

## Instalação Local

```bash
# Clonar o repositório
git clone https://github.com/jaderoliveiraa/whatsapp-manager.git
cd whatsapp-manager

# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Editar .env com suas credenciais de banco
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=whatsapp_manager
# DB_USERNAME=whatsapp
# DB_PASSWORD=suasenha
# WHATSAPP_API_URL=http://localhost:8084
# WHATSAPP_API_KEY=suochave

# Rodar migrations
php artisan migrate

# Build do frontend
npm run build

# Iniciar servidor
php artisan serve
```

Acesse: http://localhost:8000

## Docker

### WhatsApp Manager (sozinho)

```bash
cd whatsapp-manager
cp .env.docker .env
# Editar .env com suas senhas
docker compose up -d --build
```

Acesse: http://localhost:8080

### Stack Completa (Manager + API Go + PostgreSQL)

```bash
cd ..
cp .env.docker .env
# Editar .env com suas senhas
docker compose up -d --build
```

- **Manager:** http://localhost:8080
- **API Go:** http://localhost:8084

### EasyPanel

Copie os arquivos `Dockerfile`, `nginx.conf`, `docker-compose.yml` e `easypanel.yml` para o EasyPanel e configure as variáveis de ambiente pelo painel.

## Variáveis de Ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_URL` | URL do aplicativo | `http://localhost:8080` |
| `DB_HOST` | Host do PostgreSQL | `postgres` |
| `DB_DATABASE` | Nome do banco | `whatsapp_manager` |
| `DB_USERNAME` | Usuário do banco | `whatsapp` |
| `DB_PASSWORD` | Senha do banco | `secret` |
| `WHATSAPP_API_URL` | URL da API Go | `http://whatsapp-api:8084` |
| `WHATSAPP_API_KEY` | Chave da API Go | `changeme` |

## Estrutura

```
whatsapp-manager/
├── app/
│   ├── Http/Controllers/    # WhatsAppController
│   ├── Models/              # WhatsAppInstance
│   └── Services/            # WhatsAppApiService
├── config/                  # Configurações Laravel
├── database/migrations/     # Migrations do banco
├── resources/views/         # Templates Blade
│   ├── dashboard.blade.php
│   ├── instances/           # CRUD de instâncias
│   ├── messages/            # Envio de mensagens
│   └── webhook/             # Configuração de webhooks
├── routes/web.php           # Rotas
├── Dockerfile               # Build Docker
├── docker-compose.yml       # Compose local
├── nginx.conf               # Config Nginx
└── easypanel.yml            # Config EasyPanel
```

## Licença

MIT

---

**Jáder Oliveira - 88988420622**
