<?php

namespace Imamuseum\Harvester\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Imamuseum\Harvester\Contracts\HarvesterInterface;
use Imamuseum\Harvester\Commands\HarvestImages;
use Imamuseum\Harvester\Models\Object;
use Imamuseum\Harvester\Models\Source;
use Imamuseum\Harvester\Models\Asset;

class HarvestCollectionCommand extends Command
{
    use \Illuminate\Foundation\Bus\DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest:collection
                            {--setup : Populate data types from config.}
                            {--all : Run command for all objects in source (if absent only most recently changed will be processed).}
                            {--relate : Create object relationships from the source.}
                            {--source= : Source from which to sync.}
                            {--id=null : The unique id of object from source data.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest initial or update collection data from database dump.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(HarvesterInterface $harvester)
    {
        $this->harvester = $harvester;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $source = $this->option('source');
        $only_id = $this->option('id') == 'null' ? null : $this->option('id');

        // Execute command
        if ($this->option('setup')) {
            $this->info('Populating Harvester data types.');
            // create extended types
            $this->harvester->createTypes();
            return;
        }

        // Insure we have a source
        if ($source == 'null') {
            throw new Exception('No source was specified.');
        }

        // Delete old objects no longer in source
        $deleted_ids = $this->harvester->deleteOldObjects($source);
        if ($deleted_ids) {
            $this->info('Deleted '.count($deleted_ids).' from harvest database.');
        } else {
            $this->info('No objects deleted from harvest database.');
        }

        // Fetch Object ids from source that need to be updated
        $start = config('harvester.start');
        $limit = config('harvester.limit');
        $total = 0;

        do {
            // Fetch new set of object ids
            if ($only_id) {
                $ids = (object) [
                    'results' => [$only_id],
                    'total' => 1
                ];
            } else if ($this->option('all')) {
                $ids = $this->harvester->getAllIDs($source, $start, $limit);
            } else {
                $ids = $this->harvester->getUpdateIDs($source, $start, $limit);
            }

            if (!empty($ids->results)) {
                // Harvest Object Relationships
                if ($this->option('relate')) {
                    $this->info('Harvesting object relationships.');
                    $this->harvester->initialOrUpdateRelations($source, $ids->results);
                } else {
                    // Update/Initialize the Harvest database
                    $this->info('Harvesting objects.');
                    $this->harvester->initialOrUpdateObject($source, $ids->results);
                }

                $start += $limit;
                $total += $ids->total;
                $this->info("$total Loaded so far.");
            } else {
                $this->info('No more objects to update.');
            }

        } while (!empty($ids->results) && !$only_id);
    }
}
