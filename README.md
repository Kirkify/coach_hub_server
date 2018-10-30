# Server

-----------
### Table of Contents

* [How to Begin Development](#how-to-begin-development)
     - [Installing Dependencies](#installing-dependencies)
     - [Daily Briefing Documentation](https://github.com/hillaryfraley/jobbriefings#daily-briefing-documentation)
* [Environments](#environments)
     * [Docker](#docker)
          - [Create The Docker Images](#create-the-docker-images)

    
-----------
## How to Begin Development

Copy and rename the included env.example file to .env This file contains all the sensitive information (passwords and tokens) and may need to be tailored for your dev environment. The `.env` file should never be checked in as then it may be compromised. To copy and rename the .env.example file run

`cp .env.example .env`


## Installing Dependencies

Make sure you have [Composer](https://getcomposer.org/) globally installed.

Then run `composer install --ignore-platform-reqs` from project root.  This will generate all the needed php dependencies into a `vendor` folder in the root of the project.

## Generate OAuth Private Keys

The OAuth server is powered by [Laravel Passport](https://laravel.com/docs/5.6/passport). 
To Generate the OAuth keys run the following command from the project root.

`php artisan passport:keys`

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

## Configure Docker Images

Certain images require additional configurations, from your root directory navigate to the mysql docker entry folder.

`cd docker/mysql/docker-entrypoint-initdb.d`

And copy the `createdb.sql.example` file to `createdb.sql`

`cp createdb.sql.example createdb.sql`

## Build and Deploy the Docker Images

Navigate to the `docker` folder

`cd <path/to/docker/folder>`

And run `docker-compose up`