<?php namespace Fish\Logger;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;

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
    protected $description = 'Publish the logs table migration';

    public function __construct(Filesystem $files)
    {

        parent::__construct();

        $this->files = $files;
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $file = __DIR__ . '/migrations/create_mf_logs_table.php';
        $migrationsPath = $this->getMigrationsPath();

        $this->files->copy(
                $file,
                $migrationsPath . "/" . $this->getNewFileName($file)
            );

        $this->info('Published migration. run "php artisan migrate" to create the logs table');
    }

    protected function getNewFileName($file) {

         return Carbon::now()->format('Y_m_d_His').'_'.basename($file);
     }

     protected function getMigrationsPath() {

        $path = '/database/migrations';

        $app = app();
        $version = $app::VERSION[0];

        return $version=='4'?app_path() . $path:base_path() . $path;
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
