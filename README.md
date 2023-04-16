# Task for paystar

### Installation With Docker

```sh
git clone https://github.com/idanieldrew/paystar

cd paystar

# set environment
copy .env.example .env

# Start docker in os
docker-compose up --build
```

## Tests

```sh
docker-compose exec gateway php artisan test
```

## Fake data
```sh
docker-compose exec gateway php artisan migrate:fresh --seed
```

## Database Description

- PostgresQL for main database

## Webserver Description

- Nginx,because use php-fpm

## Other Description

- Has continuous integration(GitHub actions)

## Tips

- I tried to write feature tests and also use pest(Continues integration with GitHub action).
- Paystar has a problem with Pasargad Bank, that's why I'm not sure if the service is correct, but I wrote a test for it and then I made sure that this service is correct, but I can't test it in operation and also this merchant code (sign) it's not a sandbox :| (Send reason on WhatsApp)
- Documents exists in docs directory(Swagger)
- This project is api,but template html file is exist just for show
