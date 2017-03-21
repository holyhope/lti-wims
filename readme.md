Wims LTI
========

**/!\ Version *pre-alpha***

This project bring [LTI protocol](http://www.imsglobal.org/specs/ltiv2p0/implementation-guide) support to [Wims](https://sourcesup.renater.fr/projects/wimsdev/).
The main goal of this application is that Wims will be able to interact with [Moodle](http://moodle.org).

![Request from LMS][request]

[request]: http://www.plantuml.com/plantuml/png/2oufJKdDAobMqBLJyF4DLh1ISCbNA2ufJKlZ0d61eae-8PuAuPcvnKf0IO-Ga025ejJ2qjJYac8k92cWGX03gCGjCoTLeRYok2Gr5m00 "http://www.plantuml.com/plantuml/uml/2oufJKdDAobMqBLJyF4DLh1ISCbNA2ufJKlZ0d61eae-8PuAuPcvnKf0IO-Ga025ejJ2qjJYac8k92cWGX03gCGjCoTLeRYok2Gr5m00"

Technical details
-----------------

![Class Dependencies][dependencies]

[dependencies]: http://www.plantuml.com/plantuml/png/RP3D2i8m48JlUOgbzzOty52B87YGAdYG8h5PiP0cc4r14T_Th8q_BRs5dVbcTdDO6OMtMMaxZwu2IT0_XLZe1es7TFwT6AiGaXAl7PA7YpHAewC47WAYXzs7N59JWvQTgI_LKDeuUsuqZgPPbbNY-Salf9TGMsEmzjRrwSDCI9bYtqdIEsoi1taPUhPtyjXMWXsVwWbuA8r1tcu2-et_WN25KD46oRvf2TQx5HHvlECRmJi6vLZap9ojLk4pUe1tYR_wdyt4mdQEa1WPjKmtSFeB "http://www.plantuml.com/plantuml/uml/RP7B2i8m44Nt-OgXU-iVYDGY22wa2YwaI6n6BAG9PX9Gn7ytO-kZfRimzvuxZyp9I-T3eyLXPHJUfeKD7bq9ag1z2h7GDHeFazctOgn2I0gy1YGF5scKHaS9F0Hq7dOVCabr1Le6KrtAlhHnzbnh75JJD2t5yvEVIo-XiiPWvQrhq_kPC66AVIT9xx2X7kLbxBEzayUc5Epuj1w1-z8GvEb6e5_v7uXR2DNP8DdN3S5wju8yw_RuHl2CKJdc6JDdgrKyHWlmBDrL_xEPMDZW28cHGPVn3k_qEHeLl_e5?switch"

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
