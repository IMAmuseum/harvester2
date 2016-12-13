<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester2\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeTerms extends InitializeJob implements ShouldQueue
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
        logger("Intializing terms from $source...\n");
    }

    /**
     * Process each term
     */
    protected function ProcessItem($object, $model)
    {
        $sync = [];
        foreach (json_decode($object->terms) as $term) {

            // If the term value is empty, skip
            // mb_strtolower is important as non-english characters may be in use
            $term->value = mb_strtolower(trim($term->value), 'UTF-8');
            if (empty($term->value)) {
                continue;
            }

            $term_type_id = DB::table('term_types')
                ->where('term_type_name', $term->type)
                ->value('id');

            // Insure the term type was inserted from the harvest config
            if (empty($term_type_id)) {
                throw new Exception('Term type "' . $term->type . '" doesn\'t exist');
            }

            // Terms are unique and shared between objects
            $id = DB::table('terms')
                ->where('term_type_id', $term_type_id)
                ->where('term', $term->value)
                ->value('id');

            // Insert term if it doesn't exist
            if (empty($id)) {
                $id = DB::table('terms')->insertGetId([
                    'term_type_id' => $term_type_id,
                    'term' => $term->value,
                ]);
            }

            $sync[] = $id;
        }

        // no need to delete terms first b/c we are
        // finding and reusing orphaned terms
        $model->terms()->sync($sync);
    }
}
