<?php

namespace Imamuseum\Harvester2\Console\Commands;


use Exception;
use Illuminate\Console\Command;
use Imamuseum\Harvester2\Contracts\HarvesterInterface;

class Harvest extends Command
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest
                            {--source=null : source from which to sync (if absent all sources will sync)}
                            {--id=null : The unique id of object from source data (requires --source).}
                            {--recent=null : Only update recently changed records.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest data from sources into document store.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(HarvesterInterface $harvester)
    {
        $source = $this->option('source') == 'null' ? null : $this->option('source');
        $id = $this->option('id') == 'null' ? null : $this->option('id');
        $recent = $this->option('recent') == 'null' ? false : $this->option('recent');

        // Insure we have a source if id is provided
        if ($id && !$source) {
            throw new Exception('An object id was given but no source was provided.');
        }

        // Delete Old Objects
        $harvester->deleteOldObjects($source, $id);

        // Index/Update Objects
        $harvester->updateObjects($source, $id, $recent);
    }
}
