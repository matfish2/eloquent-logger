# Eloquent Logger
[![Latest Stable Version](https://poser.pugx.org/fish/eloquent-logger/v/stable)](https://packagist.org/packages/fish/eloquent-logger) [![Total Downloads](https://poser.pugx.org/fish/eloquent-logger/downloads)](https://packagist.org/packages/fish/eloquent-logger) [![Latest Unstable Version](https://poser.pugx.org/fish/eloquent-logger/v/unstable)](https://packagist.org/packages/fish/eloquent-logger) [![License](https://poser.pugx.org/fish/eloquent-logger/license)](https://packagist.org/packages/fish/eloquent-logger) [![Build Status](https://travis-ci.org/matfish2/eloquent-logger.svg?branch=master)](https://travis-ci.org/matfish2/eloquent-logger)

This Laravel 5 package adds logging functionality to Eloquent models. All changes (create, update, delete) will be recorded in a designated logs table.
Updates will record only dirty fields.

## Installation

Add to `composer.json`:

    "require": {
      "fish/eloquent-logger": "^1.0"
    }

Update Composer from the Terminal:

    composer update

Add the service provider to the the list of providers in your `app.php`

    Fish\Logger\LoggerServiceProvider

Publish migration:

    php artisan logger:init

Run migration:

    php artisan migrate

## Usage

Use the trait on relevant models. E.g:

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Fish\Logger\Logger;

    class Post extends Model
    {
      use Logger;
    }

Retrieving logs for a model:

    Post::find(1)->logs

Retrieving model from log (if the action was not `deleted`):

    Fish\Logger\Log::find(1)->loggable;

Retrieve the state of the model at a certain point of time:

    Post::find(1)->logs()->stateOn('2015-02-02 13:00:00');

To retrieve the state of a deleted model:

    Fish\Logger\Log::entity(Post::class, 1)->stateOn('2016-01-01 12:00:00');

The model will be retrieved even if the passed timestamp occured after it was already deleted.

The Log contains the following properties:

* `user_id` `integer`: User who did the action. if there is no authenticated user, set to `null`
* `action` `string`: type of action - `created`, `updated` or `deleted`
* `before` `array`: state of the model before the action. If the action is `created` set to `null`
* `after` `array`: state of the model after the action. If the action is `deleted` set to `null`
* `created_at` `datetime`: action's timestamp

### Query helpers

* `wasCreated`, `wasUpdated` or `wasDeleted` - filter by action.
* `between` - filter by date range.

Example:

    Post::find(1)->logs()
                ->wasUpdated()
                ->between('2015-01-01','2015-02-01')
                ->get();


