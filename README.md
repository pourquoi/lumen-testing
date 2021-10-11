LUMEN TESTING
-------

### backend setup

    cp api/.env.example api/.env
    docker-compose up -d

    docker exec -it lumen_backend php artisan migrate

### app setup

    cd app && yarn install
    yarn serve

### (optionnal) stripe cli

edit the stripe envs in api/.env and start the local webhook proxy

    docker-compose run stripe login
    docker-compose run stripe listen --forward-to=server:80/stripe-hooks

