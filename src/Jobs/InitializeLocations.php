<?php

namespace Imamuseum\Harvester\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeLocations extends InitializeJob implements ShouldQueue
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
        logger("Intializing locations from $source...\n");
    }

    /**
     * Process each location
     */
    protected function processItem($object, $model)
    {
        $sync = [];
        foreach (json_decode($object->locations) as $location) {
            $location->value = mb_strtolower(trim($location->value), 'UTF-8');
            if (empty($location->value)) {
                continue;
            }

            $location_type_id = DB::table('location_types')
                ->where('location_type_name', $location->type)
                ->value('id');

            // Insure the location type was inserted from the harvest config
            if (empty($location_type_id)) {
                throw new Exception('Location type "' . $location->type . '" doesn\'t exist');
            }

            // Locations are unique and shared between objects
            $id = DB::table('locations')
                ->where('location_type_id', $location_type_id)
                ->where('location', $location->value)
                ->value('id');

            // Insert location if it doesn't exist
            if (empty($id)) {
                $id = DB::table('locations')->insertGetId([
                    'location_type_id' => $location_type_id,
                    'location' => $location->value,
                ]);
            }

            $sync[] = $id;
        }

        // no need to delete locations first b/c we are
        // finding and reusing orphaned locations
        $model->locations()->sync($sync);
    }
}
