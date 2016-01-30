# Eloquent Logger

[![Build Status](https://travis-ci.org/matfish2/eloquent-logger.svg?branch=master)](https://travis-ci.org/matfish2/eloquent-logger)

This package adds logging capability to Eloquent models. All changes (create, update, delete) will be logged into a designated logs table.

## Installation

Add to `composer.json`:

    "require": {
      "fish/eloquent-logger": "^1.0"
    }

Update Composer from the Terminal:

    composer update

Add the service provider to the the list of providers in your `app.php`

    `Fish\Logger\LoggerServiceProvider`

Initialize the package (create logs table):

    php artisan logger:init

## Usage

Use the trait on relevant models. E.g:

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Fish\Logger\Logger;

    class Post extends Model
    {

      use Logger;

    }

Retrieving logs from for a model:

    Post::find(1)->logs

Retrieving model from log:

    use Fish\Logger\Log;

    ...

    Log::find(1)->loggable;

The Log contains the following properties:

* `user_id`: User who did the action. if there is no authenticated user, set to `null`
* `action`: type of action - `created`, `updated` or `deleted`
* `before`: state of the model before the action. If the action is `created` set to `null`
* `after`: state of the model after the action. If the action is `deleted` set to `null`
* `created_at`: time of the action

### Query helpers

Example:

    Post::find(1)->logs()
                ->wasUpdated()
                ->between('2015-01-01','2015-02-01')
                ->get();


