<?php

namespace Imamuseum\Harvester\Jobs;

use DB;
use Imamuseum\Harvester\Jobs\InitializeJob;

use Imamuseum\Harvester\Models\Object;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeObjects extends InitializeJob implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($source, $raw)
    {
        parent::__construct($source, $raw);
        logger("Intializing objects from $source...\n");
    }

    /**
     * process item for each object
     */
    protected function processObjects()
    {
        foreach ($this->raw_objects as $object) {
            $model = $this->fetchObject($object->object_uid, $this->source, false);

             // if object already exists update save
            if ($model) {
                $object->updated_at = $this->now();
                $model->update((array) $object);
                continue;
            }

            $object->collection = $this->source;
            $object->created_at = $this->now();
            Object::insert((array) $object);
        }
    }
}
