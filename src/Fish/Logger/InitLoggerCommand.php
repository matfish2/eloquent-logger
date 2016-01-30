<?php namespace Fish\Logger;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Schema\Blueprint;

class InitLoggerCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'logger:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the logs table';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (Schema::hasTable('mf_logs')) {
            $this->info('Already initalized');
            return;
        }

         Schema::create('mf_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('loggable_id');
            $table->string('loggable_type');
            $table->string('action');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->datetime('created_at');
        });

        $this->info('Created logs table');
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
}
