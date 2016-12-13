<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester2\Jobs\InitializeJob;
use Log;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeRelated extends InitializeJob implements ShouldQueue
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
        logger("Intializing related objects from $source...\n");
    }

    /**
     * Process each relationship
     */
    protected function processItem($object, $model)
    {
        $sync = [];
        foreach (json_decode($object->related) as $relation) {

            if (isset($relation->number)) {
                $related_id = DB::table('object_term as ot')
                    ->join('terms as t', 't.id', '=', 'ot.term_id')
                    ->join('term_types as tt', 'tt.id', '=', 't.term_type_id')
                    ->where('tt.term_type_name', 'original accession number')
                    ->where(DB::raw('lower(t.term)'), strtolower($relation->number))
                    ->select('ot.object_id as id')
                    ->value('id');

                if (!$related_id) {
                    $related_id = DB::table('objects')
                        ->where(DB::raw('lower(accession_num)'), strtolower($relation->number))
                        ->where('collection', $this->source)
                        ->value('id');
                }

                // Insure the text type was inserted from the harvest config
                if (empty($related_id)) {
                    // throw new Exception('Text type ' . $number . ' doesn\'t exist');
                    Log::warning(
                        "Failed to find related object: source - ".
                        $this->source.", number - ".
                        $relation->number.", object_uid - ".
                        $model->object_uid."\n"
                    );

                    continue;
                }

                $sync[$related_id] = [
                    'notes' => isset($relation->notes) ? $relation->notes : null,
                    'relationship' => isset($relation->relationship) ? $relation->relationship : null,
                ];
            }
        }

        // This is intentionally owns instead of ownedBy
        $model->owns()->sync($sync);
    }
}
