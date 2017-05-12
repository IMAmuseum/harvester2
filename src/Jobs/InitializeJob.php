<?php

namespace Imamuseum\Harvester2\Jobs;

use App\Jobs\Job;
use Exception;
use Carbon\Carbon;
use DB;
use Imamuseum\Harvester2\Models\Object;

abstract class InitializeJob extends Job
{
    protected $object_model;
    protected $raw_objects;
    protected $step = 1;
    protected $source;
    protected $total;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $source,
        $raw_objects,
        Object $object_model = null
    ) {
        $this->object_model = isset($object_model) ? $object_model : new Object;
        $this->raw_objects = $raw_objects;
        $this->source = $source;
        $this->total = count($raw_objects);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->displayGoal();

        // Set deadlock timeout to one minute (sqlite will wait up to a minute for database lock to be released)
        // This is the default setting with php pdo sqlite2 but with sqlite3 we must set it manually.
        $pdo = DB::connection(config('proficio.database_connection'))->getPdo();
        $pdo->setAttribute($pdo::ATTR_TIMEOUT, 60000);

        // Transaction can produce 15x faster results with Sqlite
        DB::transaction(function () {
	       $this->processObjects();
        });
    }

    /**
     * process item for each object
     */
    protected function processObjects()
    {
        foreach ($this->raw_objects as $object) {
            $collection = isset($object->collection) ? $object->collection : $this->source;
            $model = $this->fetchObject($object->object_uid, $collection);
            $this->processItem($object, $model);
        }
    }

    /**
     * fetch model from given object_uid
     */
    protected function fetchObject($object_uid, $collection, $failIfNotFound = true)
    {
        $model = $this->object_model->where('collection', $collection)
                ->where('object_uid', $object_uid)
                ->first();

        if ($failIfNotFound && empty($model)) {
            throw new Exception("Object with object_uid of $object_uid associated with collection $collection does not exist.");
        }

        return $model;
    }

    /**
     * Helper function to get current timestamp
     */
    protected function now()
    {
        return Carbon::now();
    }

    /**
     * display goal for user feed back
     */
    protected function displayGoal()
    {
        logger( "Processing: " . $this->total . "\n");
    }
}
