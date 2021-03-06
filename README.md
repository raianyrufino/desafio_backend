## Instalação 

Clonagem do diretório:
```
git clone https://github.com/raianyrufino/desafio_backend
```

Acesse a raiz do projeto `cd desafio_backend`;

Baixe as dependências do projeto via composer:
```
composer install
```

Configure o autoload
```
composer dump-autoload
```

## Configuração
Criação do arquivo de configuração local:
```
cp .env.example .env
```

Criação do `APP_KEY`:
```
php artisan key:generate
```

Dentro do arquivo gerado, o que deve ser alterado (O `APP_KEY` foi gerado automático no passo anterior):
```
APP_ENV=local
APP_DEBUG=true
APP_KEY=SomeRandomString
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

## Executar Limpeza de Cache
```
php artisan optimize
```

## Executar Migrations
```
php artisan migrate
```

## Executar Seeders
```
php artisan db:seed 
```

## Executar Testes
```
php artisan test
```

## Executar Aplicação
```
php artisan serve
```

### Payload

#### Criar Usuário
POST /users

```json
{
    "name" : "Ana",
    "password" : "123123",
    "email" : "ana@gmail.com",
    "cpf_cnpj" : "12312312387",
    "type" : "COMUM"
}
```

#### Depositar
POST /users/{id}/deposit

```json
{
    "value" : 300.00,
}
```

#### Transferir
POST /users/{id}/transfer

```json
{
    "payee_id" : 2,
    "value" : 250.00
}
```

#### Consultar saldo
GET /users/{id}
