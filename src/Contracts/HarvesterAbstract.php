<?php

namespace Imamuseum\Harvester\Contracts;

use Imamuseum\Harvester\Models\Object;
use Imamuseum\Harvester\Models\Asset;
use Imamuseum\Harvester\Models\Source;

/**
 * This is a bit of a middle man
 */
abstract class HarvesterAbstract
{
    public function createTypes()
    {
        // get config array for harvester types
        $configTypes = config('harvester.types');
        // loop through types and insert
        foreach ($configTypes as $keyType => $valueType) {
            foreach($valueType as $type) {
                $typeModel = $keyType . "_types";
                $typeName = $keyType . "_type_name";
                $typeDesc = $keyType . "_type_desc";
                \DB::table($typeModel)->insert([
                    $typeName => $type['name'],
                    $typeDesc =>  $type['desc']
                ]);
            }
        }
    }

    public function deleteOldObjects($source)
    {
        // Get objects no longer in the source.
        // These will be deleted from the harvester database
        $start = 0;
        $limit = 10000;
        $removed_ids = [];
        $to_delete = \DB::table('objects')->where('collection', $source)->pluck('object_uid');

        // incrementally remove ids that don't need to be deleted
        // We paginate this process to prevent hitting a memory limit
        do {
            $import_uids = $this->getAllIDs($source, $start, $limit);
            $to_delete = array_diff($to_delete, $import_uids->results);
            $start += $limit;

        } while (!empty($import_uids->results));


        // Delete the objects
        if (!empty($to_delete)) {
            // Get the harvest ids of the objects to be deleted
            $removed_ids = \DB::table('objects')->whereIn('object_uid', $to_delete)->pluck('id');

            // Delete the objects
            foreach ($to_delete as $object_uid) {
                $object = Object::where('collection', $source)
                    ->where('object_uid', '=', $object_uid)
                    ->first();

                Source::where('object_id', '=', $object->id)->delete();
                Asset::where('object_id', '=', $object->id)->delete();

                $object->delete();
            }
        }

        return $removed_ids;
    }


    /**
     * Process each object
     * @author Daniel Keller
     */
    public function createOrUpdateObjects($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeObjects($source, $raw_objects_results));
    }

    /**
     * Process each Term per Object
     * @author Daniel Keller
     */
    public function createOrUpdateTerms($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeTerms($source, $raw_objects_results));
    }

    /**
     * Process each Actor per Object
     * @author Daniel Keller
     */
    public function createOrUpdateActors($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeActors($source, $raw_objects_results));
    }

    /**
     * Process each Texts per Object
     * @author Daniel Keller
     */
    public function createOrUpdateTexts($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeTexts($source, $raw_objects_results));
    }

    /**
     * Process each Locations per Object
     * @author Daniel Keller
     */
    public function createOrUpdateLocations($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeLocations($source, $raw_objects_results));
    }

    /**
     * Process each Dates per Object
     * @author Daniel Keller
     */
    public function createOrUpdateDates($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeDates($source, $raw_objects_results));
    }


    /**
     * Process each Asset per Object
     * @author Daniel Keller
     */
    public function createOrUpdateAssets($source, $raw_objects_results)
    {
        dispatch(new \App\Jobs\InitializeAssets($source, $raw_objects_results));
    }

    /**
     * Process each Relation per Object based on proficio config
     * @author Daniel Keller
     */
    public function initialOrUpdateRelations($source, $ids)
    {
        dispatch(new \App\Jobs\InitializeRelated($source, $raw_objects_results));
    }
}
