# Cyca

![](https://static.getcyca.com/getcyca.com/screenshots/cyca-0.3.png)

## About

Cyca is a web-based, desktop-centric, multi-user bookmarks and feeds manager.

### Key features

- Organisation of bookmarks and feed in a folders hierarchy
    - Drag'n'drop for folders and bookmarks
    - You can create no folder at all, or a huge hierarchy
    - You can add as many bookmarks to a folder as you want
- Unobtrusive
    - No notification, popup or anything eye-catching, except number of unread
      items right to each folder and document
    - Simple forms to add folders and bookmarks, simple buttons to subscribe
      to feeds
    - No personal data collected, no telemetry, no query to any external server
      except to update documents and feeds
- Auto-update documents and feeds
    - Auto-purging unused documents and feeds
    - Respects standards and good-practices to avoid unwanted network traffic
- Documents and feeds are mutual to all users
    - Bookmarks (which points to documents), subscriptions (which points to 
      feeds) and unread feed item are private and not known to other users
    - Already existing documents and feeds are quicker to display when adding
      a bookmark
- Desktop-centric
    - Features a three-columns layout which provides direct access to any
      information you need quickly
    - Features a details panels that changes with context, which provides forms
      and informations at just about the right time

### Author's notes

Bookmarks and feeds naturally get along, so it was obvious to me to manage them
from the same application.

The intent behind Cyca is to put light on feeds you can miss: as Cyca can
discover feeds in your bookmarks, when you bookmark a URL, you automatically
gain visibility to declared feeds. You don't even need to know about RSS or Atom
at all, which could introduce new people to these technologies.

## Installation

I don't recommand any installation method in particular, except the one you're
most comfortable with.

### Via Docker

Please read docker specific documentation from 
[Cyca's docker repository](https://github.com/RichardDern/cyca_docker_compose).

### Manually

#### Requirements

As Cyca is built with Laravel, it has the same requirements. For an up-to-date
details about them, you can read [Laravel's installation page](https://laravel.com/docs).

Additionally, you will need to install a [Redis](https://redis.io/) server, [PHP
Redis extension](https://github.com/phpredis/phpredis), a database server (Cyca
support SQLite, MySQL/MariaDB and SQLServer - use of SQLite is not recommanded
for large instances), [nodejs](https://nodejs.org/en/) and the following extra libraries:

- imagick
- curl
- php-gd
- php-intl

As well as [composer](https://getcomposer.org).

If you plan using additional software like memcached, dynamodb or other Laravel
supported third-party tools, please read corresponding documentation for 
installation. Don't forget to edit Cyca's configuration accordingly.

#### Installation

##### Web server

You can install and run your own webserver the way you're used to. Make your
host point to the ```/public``` directory, and make sure the ```/storage```
directory can be written to by the webserver. You can also read [Laravel's 
installation page](https://laravel.com/docs/8.x/installation#web-server-configuration) to adapt your webserver configuration.
You can use the ```/resources/docker/nginx.conf``` file as a starting point.
Please note that you need to configure a ```/socket.io``` directory for using
websockets.

##### Cyca

Install dependencies:

```composer install```

Create the main configuration file:

```cp .env.example .env```

Generate an application key (you can read [Laravel's installation page](https://laravel.com/docs) for more informations):

```php artisan key:generate```

And modify the ```.env``` file to suit your needs. Be sure to set ```APP_URL```
to the URL Cyca will be reached.

Other important settings are ```LARAVEL_ECHO_SERVER_REDIS_HOST```, 
```DB_CONNECTION``` and ```REDIS_HOST``` which must be set to serving hostname 
(```localhost``` if you run everything on the same server).

Everything else should have sensible defaults. You can also walk through the
```/config``` directory to customize your installation further, but defaults
should fit any situation.

You will need a process monitor, such as Supervisor or Systemd, to run the queue
server and the websocket server. You will find sample configuration files in the
```/examples/supervisor``` directory for both processes. Be sure to edit them
before you copy them in the ```/etc/supervisor.d/conf``` directory, especially
the paths to Cyca's directory.

You can get more informations about installing and using Supervisor from 
[Laravel Horizon documentation](https://laravel.com/docs/8.x/horizon#deploying-horizon), Horizon being Cyca's queue manager.

You should be now ready to point your browser to Cyca's URL and create your
first user !

## License

Cyca is licensed under the GNU GPL in its more recent version. You will find a
copy of this license in the LICENSE file at the root of Cyca's archive.

## Contact and links

Cyca's creator is Richard Dern.

- Website: https://getcyca.com/
- GitHub: https://github.com/RichardDern
