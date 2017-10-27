Wims LTI
========

** /!\ Version *beta* **

This project bring [LTI protocol](http://www.imsglobal.org/specs/ltiv2p0/implementation-guide) support to [Wims](https://sourcesup.renater.fr/projects/wimsdev/).
The main goal of this application is that Wims will be able to interact with [Moodle](http://moodle.org).

Installation
------------

This applicaton is dependant from Wims.
It should be linked to a valid database, and the Wims instance should be active.

First of all, please start [Wims](https://sourcesup.renater.fr/projects/wimsdev/) and a database.

### Installation on a dedicated server

Classic installation requires a dedicated web server. See below for more details.

#### Requirements

* Web server : [Apache](https://httpd.apache.org) recommended
* [PHP 7+](http://php.net) with [OAuth](https://secure.php.net/manual/en/book.oauth.php) and [PDO](https://secure.php.net/manual/en/book.pdo.php) extensions
* [Composer](https://getcomposer.org/) (see `composer.json` to see dependencies)

#### How to install?

1. Download the latest version to `/var/www`

   ```bash
   git clone https://github.com/holyhope/lti-wims.git /var/www
   ```

2. Populate the database wims `/var/www/db/install.sql`

3. share wims folder with lti-wims

   ```bash
   ln -s /home/wims /var/wims
   ```

4. Write `/var/www/config.php`

   ```php
   <?php
   namespace LTI;
   
   const ROOT_PATH     = __DIR__;
   const TEMPLATE_PATH = ROOT_PATH . '/templates';
   const CLASS_PATH    = ROOT_PATH . '/classes';
   
   const DB_NAME     = 'wims_lti';
   const DB_USER     = 'lti';
   const DB_PASSWORD = 'myPassword';
   const DB_HOST     = '192.168.1.15';
   const DB_PORT     = '3306';
   const DB_DRIVER   = 'mysql';  // Should be one of PDO::getAvailableDrivers()
   const DB_PREFIX   = '';       // The prefix of table
   ```

### Installation using docker

This application contains a dockerfile, feel free to change and build your own image.

#### Requirements

* [Docker](https://docs.docker.com/engine/reference/builder/)

You can also use the [wims](https://github.com/afranke/wims) and [postgres](https://hub.docker.com/_/postgres/) docker image.

```bash
docker run -d -p 8080:80 -v /mnt/wims:/var/www wims # Start wims on port 80
docker run -d -p 5432:5432 -e POSTGRES_DB=wims_lti -e POSTGRES_USER=lti -e POSTGRES_PASSWORD=myPassword postgres # Start postgresql on port 5432
```

#### How to install?

1. Download the latest version to `~/wims-lti`

   ```bash
   git clone https://github.com/holyhope/lti-wims.git /var/www
   ```

2. Populate the database wims `~/wims-lti/db/install.sql`

   ```bash
   psql -f ~/wims-lti/db/install.sql -U lti wims_lti
   ```

3. Write `~/wims-lti/config.php`

   ```php
   <?php
   namespace LTI;
   
   const ROOT_PATH     = __DIR__;
   const TEMPLATE_PATH = ROOT_PATH . '/templates';
   const CLASS_PATH    = ROOT_PATH . '/classes';
   
   const DB_NAME     = 'wims_lti';
   const DB_USER     = 'lti';
   const DB_PASSWORD = 'myPassword';
   const DB_HOST     = 'localhost';
   const DB_PORT     = '5432';
   const DB_DRIVER   = 'pgsql';  // Should be one of PDO::getAvailableDrivers()
   const DB_PREFIX   = '';       // The prefix of table
   ```

4. Build and run the docker image

   ```bash
   docker build -t lti-wims ~/wims-lti
   docker run -d -v /mnt/wims:/var/wims -p 80:80 lti-wims
   ```

How to configure
----------------

In order to use LTI Wims, you will need of course all [previous requirements](#requirements), but also a Tool Consumer.

### Moodle as the Tool Consumer

Add an external activity in your course with the server data.
You do not need an OAuth token, it is disabled ([Issue#3](https://github.com/holyhope/lti-wims/issues/3)).

Technical details
-----------------

### Requests

Here is the flow of one standard request from a client to wims through LTI Wims.

![Request from LMS][request]

### Internal classes

![Class Dependencies][dependencies]

Router will catch all request and call the right endpoint. Router find dynamically controller thanks to the endpoint.
All data are store in a classical database (PostGreSQL is recommended).

### Go further...

More details.

#### Connexion with wims

LTI Wims does not need an HTTP access to Wims. Only users do.

But it get information from Wims directly from its filesystem.
It read mainly users' session files.
That is the reason why you need to mount a shared volume between docker instances.

[request]: http://www.plantuml.com/plantuml/png/2oufJKdDAobMqBLJyF4DLh1ISCbNA2ufJKlZ0d61eae-8PuAuPcvnKf0IO-Ga025ejJ2qjJYac8k92cWGX03gCGjCoTLeRYok2Gr5m00 "http://www.plantuml.com/plantuml/uml/2oufJKdDAobMqBLJyF4DLh1ISCbNA2ufJKlZ0d61eae-8PuAuPcvnKf0IO-Ga025ejJ2qjJYac8k92cWGX03gCGjCoTLeRYok2Gr5m00"

[dependencies]: http://www.plantuml.com/plantuml/png/RP3D2i8m48JlUOgbzzOty52B87YGAdYG8h5PiP0cc4r14T_Th8q_BRs5dVbcTdDO6OMtMMaxZwu2IT0_XLZe1es7TFwT6AiGaXAl7PA7YpHAewC47WAYXzs7N59JWvQTgI_LKDeuUsuqZgPPbbNY-Salf9TGMsEmzjRrwSDCI9bYtqdIEsoi1taPUhPtyjXMWXsVwWbuA8r1tcu2-et_WN25KD46oRvf2TQx5HHvlECRmJi6vLZap9ojLk4pUe1tYR_wdyt4mdQEa1WPjKmtSFeB "http://www.plantuml.com/plantuml/uml/RP7B2i8m44Nt-OgXU-iVYDGY22wa2YwaI6n6BAG9PX9Gn7ytO-kZfRimzvuxZyp9I-T3eyLXPHJUfeKD7bq9ag1z2h7GDHeFazctOgn2I0gy1YGF5scKHaS9F0Hq7dOVCabr1Le6KrtAlhHnzbnh75JJD2t5yvEVIo-XiiPWvQrhq_kPC66AVIT9xx2X7kLbxBEzayUc5Epuj1w1-z8GvEb6e5_v7uXR2DNP8DdN3S5wju8yw_RuHl2CKJdc6JDdgrKyHWlmBDrL_xEPMDZW28cHGPVn3k_qEHeLl_e5?switch"
