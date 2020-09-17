## Installation

You need docker and docker-compose.

After cloning the repository, you might want to check the following file:

```resources/docker/Dockerfile```

It is pre-configured for running Cyca with MariaDB, redis and nginx, but there
are examples to run other database server and additionnal software.

Once satisfied, run:

```docker-compose build```

This can take some time.

Then, create your ```.env``` file and modify it according to your needs:

```cp .env.example .env```

Run the containers:

```docker-compose up```

If there was no errors, install dependencies, create application key and run the 
migrations:

```docker exec cyca_app_1 php composer install```
```docker exec cyca_app_1 php artisan key:generate```
```docker exec cyca_app_1 php artisan migrate```

Replace ```cyca_app_1``` with your actual container name if it differs.

You can now stop the containers using Control+C and run them again for good:

```docker-compose up -d```

You should be able to connect to your instance and register a new user. Cyca's
webserver listens to the port 8080.
