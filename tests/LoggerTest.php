<?php

use Orchestra\Testbench\TestCase;
use Models\User;
use Models\Author;
use Models\Post;
use Models\Comment;
use Fish\Logger\Log;
use Carbon\Carbon;

class LoggerTest extends TestCase
{

   /**
   * Setup the test environment.
   */
   public function setUp()
   {
    parent::setUp();

        // Create an artisan object for calling migrations
    $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');

        // Call migrations specific to our tests, e.g. to seed the db
    $artisan->call('migrate', array(
        '--database' => 'testbench',
        '--path'     => '../tests/database/migrations',
        ));

    $artisan->call('logger:init');
    $user = User::create(['name'=>'Dolly','email'=>'dolly@example.com']);
    $author = Author::create(['user_id'=>$user->id,'role'=>'editor']);

}

    /**
   * Define environment setup.
   *
   * @param  Illuminate\Foundation\Application    $app
   * @return void
   */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        // set up database configuration
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
            ));

    }


    /** @test */
    public function it_records_model_creation()
    {
     $user = User::find(1);

     $log = $user->logs()->wasCreated()->first();

     $this->assertEquals($log->before, null);
     $this->assertEquals($log->after['name'], 'Dolly');
     $this->assertEquals($log->after['email'], 'dolly@example.com');
     $this->assertEquals($log->user_id, null);

 }

 /** @test */
 public function it_records_model_update()
 {

    $user = User::find(1);
    Auth::login($user);

    $user->name = "Yossi";
    $user->save();

    Auth::logout();

    $author = Author::find(1);
    $author->role = 'admin';
    $author->save();

    $log = $user->logs()->wasUpdated()->first();

    $this->assertEquals($log->before, ['name'=>'Dolly']);
    $this->assertEquals($log->after, ['name'=>'Yossi']);
    $this->assertEquals($log->user_id, 1);


    $log = $author->logs()->wasUpdated()->first();

    $this->assertEquals($log->before,['role'=>'editor']);
    $this->assertEquals($log->after,['role'=>'admin']);
    $this->assertEquals($log->user_id, null);
}


    /**
     * Get Sluggable package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Fish\Logger\LoggerServiceProvider'];
    }

/** @test */
public function it_records_model_delete()
{

 User::find(1)->delete();

 $log = Log::wasDeleted()->where('loggable_type','Models\User')->first();

 $this->assertEquals($log->before['name'], 'Dolly');
 $this->assertEquals($log->before['email'], 'dolly@example.com');
 $this->assertEquals($log->after, null);

 $log = Log::wasDeleted()->where('loggable_type','Models\Author')->first();

 $this->assertEquals($log->before['role'], 'editor');
 $this->assertEquals($log->after, null);

}

/** @test */
public function it_filters_logs_by_date_range()
{

 $start = Carbon::now()->startOfDay();
 $end = Carbon::now()->endOfDay();

 $log = Log::wasCreated()->between($start, $end)->get();

 $this->assertEquals($log->count(), 2);

 $end = Carbon::now()->startOfDay();

 $log = Log::wasCreated()->between($start, $end)->get();

 $this->assertEquals($log->count(), 0);

}

/** @test */
public function it_rerieves_loggable_entity_from_log()
{

 $log = Log::first();

 $user = $log->loggable;

 $log = Log::find(2);

 $author = $log->loggable;

 $this->assertEquals(get_class($user), 'Models\User');
 $this->assertEquals(get_class($author), 'Models\Author');

}

}
