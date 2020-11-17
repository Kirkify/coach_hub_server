Shield: [![CC BY 4.0][cc-by-shield]][cc-by]

This work is licensed under a
[Creative Commons Attribution 4.0 International License][cc-by].

[![CC BY 4.0][cc-by-image]][cc-by]

[cc-by]: http://creativecommons.org/licenses/by/4.0/
[cc-by-image]: https://i.creativecommons.org/l/by/4.0/88x31.png
[cc-by-shield]: https://img.shields.io/badge/License-CC%20BY%204.0-lightgrey.svg

# Server

-----------
### Table of Contents

* [Beginning Server Development](#Beginning-Server-Development)
     - [Adding The Environment File](#Adding-The-Environment-File)
     - [Installing Dependencies](#Installing-Dependencies)
* [Environments](#Environments)
     * [Docker](#Docker)
          - [Create The Docker Images](#Create-The-Docker-Images)
          - [Configuring The Docker Images](#Configuring-The-Docker-Images)
     * [Homestead](#Homestead)
          - [Homestead Dependencies](#Homestead-Dependencies)
* [Useful Commands](#Useful-Commands)
          - [Generate OAuth Private Keys](#Generate-OAuth-Private-Keys)    
-----------
## Beginning Server Development

Welcome to server development, please refer to the table of contents above for further information on server development.

### Adding The Environment File

Copy and rename the included env.example file to .env This file contains all the sensitive information (passwords and tokens) and may need to be tailored for your dev environment. The `.env` file should never be checked in as then it may be compromised. To copy and rename the .env.example file run

`cp .env.example .env`

### Installing Dependencies

Make sure you have [Composer](https://getcomposer.org/) globally installed.

Then run `composer install --ignore-platform-reqs` from project root.  This will generate all the needed php dependencies into a `vendor` folder in the root of the project.

## Environments

Currently there are two different ways to setup the server development environment.  The first method is docker
which is currently the preferred method, but requires further investigation as we currently cannot figure out how to setup breakpoints.
in PHP Storm.

https://laravel.com/docs/5.3/homestead#installation-and-setup

The second method is a tool called [Laravel Homestead](https://laravel.com/docs/master/homestead).  This is basically your server environment
hosted on a virtual machine.

## Docker

Docker can be used 

### Create The Docker Images

Navigate to the docker folder

`cd docker`

And create an `.env` file

`cp .env.example .env`

Make sure you have [Docker](https://getcomposer.org/) installed and running as well as the
Compose tool [Docker Compose](https://docs.docker.com/compose/), compose should be automatically
installed with Docker on Mac and Linux.

### Configuring The Docker Images

Certain images require additional configurations, from your root directory navigate to the mysql docker entry folder.

`cd docker/mysql/docker-entrypoint-initdb.d`

And copy the `createdb.sql.example` file to `createdb.sql`

`cp createdb.sql.example createdb.sql`

### Build and Deploy the Docker Images

Navigate to the `docker` folder

`cd <path/to/docker/folder>`

And run `docker-compose up`

## Homestead

Laravel Homestead is an official, pre-packaged Vagrant box that provides you a wonderful development environment without requiring you to install PHP, a web server, and any other server software on your local machine. No more worrying about messing up your operating system! Vagrant boxes are completely disposable. If something goes wrong, you can destroy and re-create the box in minutes!

### Homestead Dependencies

This tool requires a few dependencies: [Virtual Box 5.x](https://www.virtualbox.org/wiki/Downloads) and [Vagrant](https://www.vagrantup.com/).
Once all these dependencies have been installed we can generate the Homestead.yaml file by running the command.

`php vendor/bin/homestead make`

## Useful Commands

Below is a list of usefull commands which can help with development.

### Generate OAuth Private Keys

The OAuth server is powered by [Laravel Passport](https://laravel.com/docs/5.6/passport). 
To Generate the OAuth keys run the following command from the project root.

`php artisan passport:keys`