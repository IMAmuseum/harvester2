<?php

namespace Imamuseum\Harvester\Jobs;

use DB;
use Imamuseum\Harvester\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeActors extends InitializeJob implements ShouldQueue
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
        logger("Intializing actors from $source...\n");
    }

    /**
     * Process each actor
     */
    protected function processItem($object, $model)
    {
        $sync = [];
        foreach (json_decode($object->actors) as $actor) {

            // actors are unique and shared between objects
            $id = DB::table('actors')
                ->where('actor_name_display', $actor->actor_name_display)
                ->value('id');

            if (!$id) {
                $actor_uid = isset($actor->actor_uid) ? $actor->actor_uid : $this->source . ':' . $model->id . ':' . $actor->sequence;

                $id = DB::table('actors')->insertGetId([
                    'actor_uid' => $actor_uid,
                    'actor_name_display' => $actor->actor_name_display,
                    'created_at' => $this->now(),
                    'updated_at' => $this->now()
                ]);
            }

            $sync[$id] = [
                'role' => $actor->role,
                'sequence' => $actor->sequence,
            ];
        }

        // no need to delete actors first b/c we are
        // finding and reusing orphaned actors
        $model->actors()->sync($sync);
    }
}
