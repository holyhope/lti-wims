Wims LTI
========

This project bring [LTI protocol](http://www.imsglobal.org/specs/ltiv2p0/implementation-guide) support to [Wims](https://sourcesup.renater.fr/projects/wimsdev/).
The main goal of this application is that Wims will be able to interact with [Moodle](http://moodle.org).

Installation
------------

This applicaton is dependant from Wims.
It should be linked to a valid database, and the Wims instance should be active.

### Normal

Classic installation requires a dedicated web server. See below for more details.

#### Requirements

* Web Server : [Apache](https://httpd.apache.org) recommended
* [PHP 7+](http://php.net) with [OAuth](https://secure.php.net/manual/en/book.oauth.php) and [PDO](https://secure.php.net/manual/en/book.pdo.php) extensions
* [Composer](https://getcomposer.org/) (see `composer.json` to see dependencies)

#### How to install ?

1. Download the latest version
2. Copy all files in `/var/www`
3. Write `config.php`
4. Write `/etc/apache2/site-available/wims-lti.conf`
5. Activate site with `a2ensite wims-lti`

### Dockerized

This application come with a [dockerfile](https://docs.docker.com/engine/reference/builder/), feel free to change and build your own image.
