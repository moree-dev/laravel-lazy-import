# Laravel Lazy Import

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Docker](#docker)
  - [Local Machine](#local-machine)
- [Configuration](#configuration)
- [Import Data](#import-data)

<a name="introduction"></a>
## Introduction
This project gives you the ability to import data into database from a massive file such as JSON, XML, CSV, etc.
Regard to taking the stream reading file strategy, and queuing the input, there is no limit how big the file could be.
And If the process stopped, it will continue the process again if queue system were working.
Currently, the project only supports JSON but the other drivers will be provided soon!  

<a name="requirements"></a>
## Requirements
The best environment to run this project is docker. So the only things that you need to run the project is docker and
docker-compose. But if you don't want to run this project on docker, you will need **PHP 8.0+** or newer, 
**Composer**, and **MySQL**.

<a name="installation"></a>
## Installation
<a name="docker"></a>
### Docker
I developed this project using **[Laravel Sail](https://laravel.com/docs/8.x/sail)**. So at the first place you need to
install the dependencies. But if you don't want to deal with installing `composer` locally, you can install them
with an ephemeral container from composer official image. Just follow the following steps:

- `cd` into project's directory.
- Copy the `.env.example` to `.env`. 
- run `docker run --rm -v $(pwd):/app composer install`
- run `./vendor/bin/sail up` and wait until all containers been created and stared. 
- run `php artisan key:generate`
- run `php artisan migrate`
- 
<a name="local-machine"></a>
### Local Machine
As it was mentioned before, you can run this project on your local machine. For that, you can go through following 
steps: 
- `cd` into project's directory.
- Copy the `.env.example` to `.env`.
- run `composer update`
- Create the database on MySQL and fill the database configurations on `.env` file.
- run `php artisan key:generate`
- run `php artisan migrate`

<a name="configuration"></a>
## Configuration
All configurations could be set within `environment variables`. The following list are these configurations
which the names of these variables, represent their use-case:
- DATA_SOURCE_CHARACTER_LENGTH 
- DATA_IMPORT_STRICT_VALIDATION
- MIN_CLIENT_AGE
- MAX_CLIENT_AGE
- CLIENT_AGE_ALLOW_UNKNOWN
- CLIENT_CARD_NUMBER_PATTERN

<a name="import-data"></a>
## Import Data
There is an example file `customers.json` in `storage` directory. The use-case of this file
is to run unit tests and also run the actual import as an instance. To start importing this
file you can run the following command:

`php artisan data-import:define "customers.json" "\App\Services\DataImport\Drivers\Client"`

The first argument is the *Relative path* of the file inside the storage folder. And the second argument is for
defining the `Driver` which imports data into database.

The import process is based on Laravel Queues. So the queue worker should be running to import data. But in production
environment, consider using a service monitor to be sure that always the queue worker is running. To see how queues are 
working, logs, etc. you can check them from Laravel Telescope that is provided in project.    
