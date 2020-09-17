# Cyca

![](https://static.getcyca.com/getcyca.com/screenshots/cyca-0.1.0.png)

## About

Cyca is a web-based, multi-user bookmarks and feeds manager.

Bookmarks and feeds naturally get along, so it was obvious to me to manage them
from the same application.

In Cyca, feeds are attached to documents. When you browse your bookmarks, the
right panel shows you corresponding feed items found in feeds attached to your
bookmarks.

The intent behind that is to put light on feeds you can miss: as Cyca can
discover feeds in your bookmarks, when you bookmark a URL, you automatically
gain visibility to declared feeds.

Of course, any feed reader do that, but a feed reader is just a feed reader, 
where Cyca is also a bookmarks manager.

This intent is also the reason why, by default, you follow all discovered feeds,
and manually choose to ignore some of them.

Cyca is non-obtrusive: you won't get any notification upon new feed items added.
The number of unread items is presented right to each folder and bookmark.

Your bookmarks and feeds will be automatically updated by Cyca using a cronjob
(scheduled task).

## Installation

You need [docker](https://www.docker.com) and [docker-compose](https://docs.docker.com/compose/),
although you can install and run your own services the way you want (webserver,
redis, etc.)

After cloning the repository, you might want to check the following file:

```resources/docker/Dockerfile```

It is pre-configured for running Cyca with MariaDB, redis and nginx, but there
are examples to run other database server and additionnal software.

Once satisfied, run:

```docker-compose build```

This can take some time.

Note that this step is required until an official docker image for Cyca is 
released.

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
