<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester2\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeDates extends InitializeJob implements ShouldQueue
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
        logger("Intializing dates from $source...\n");
    }

    /**
     * Process each date
     */
    protected function processItem($object, $model)
    {
        foreach (json_decode($object->dates) as $date) {
            $date->value = trim($date->value);
            if (empty($date->value)) {
                continue;
            }

            $date_type_id = DB::table('date_types')
                ->where('date_type_name', $date->type)
                ->value('id');

            // Insure the date type was inserted from the harvest config
            if (empty($date_type_id)) {
                throw new Exception('Date type "' . $date->type . '" doesn\'t exist');
            }

            // Dates are unique and shared between objects
            $id = DB::table('dates')
                ->where('date_type_id', $date_type_id)
                ->where('date', $date->value)
                ->value('id');

            // Insert date if it doesn't exist
            if (empty($id)) {
                $id = DB::table('dates')->insertGetId([
                    'date_type_id' => $date_type_id,
                    'date' => $date->value,
                ]);
            }

            $sync[] = $id;
        }

        // no need to delete dates first b/c we are
        // finding and reusing orphaned dates
        $model->dates()->sync($sync);
    }
}
