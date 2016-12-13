<?php

namespace Imamuseum\Harvester\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeAssets extends InitializeJob implements ShouldQueue
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
        logger("Intializing sources from $source...\n");
    }

    /**
     * Process each term
     */
    protected function ProcessItem($object, $model)
    {
        // remove old sources and assets
        // TODO DK: these 2 lines increase the processing time 10 fold
        $model->source()->delete();
        $model->assets()->delete();

        foreach (json_decode($object->assets) as $asset) {
            $asset_type_id = DB::table('asset_types')
                ->where('asset_type_name', $asset->type)
                ->value('id');

            // Insure the asset type was inserted from the harvest config
            if (empty($asset_type_id)) {
                throw new Exception('Asset type ' . $asset->type . ' doesn\'t exist');
            }

            // Source/Object combinations must be unique
            $exist = DB::table('sources')
                ->where('object_id', $model->id)
                ->where('source_uri', $asset->path)
                ->exists();

            if (!$exist) {
                $source_id = DB::table('sources')->insertGetId([
                    'object_id' => $model->id,
                    'origin_id' => $object->origin_id,
                    'source_uri' => $asset->path,
                    'source_sequence' => $object->sequence,
                    'created_at' => $this->now(),
                    'updated_at' => $this->now(),
                ]);


                DB::table('assets')->insertGetId([
                    'object_id' => $model->id,
                    'source_id' => $source_id,
                    'asset_type_id' => $asset_type_id,
                    'asset_sequence' => $object->sequence,
                    'asset_file_uri' => $asset->path,
                    'caption' => isset($asset->caption) ? $asset->caption : '',
                    'description' => isset($asset->description) ? $asset->description : '',
                    'created_at' => $this->now(),
                    'updated_at' => $this->now(),
                ]);
            }
        }
    }
}
