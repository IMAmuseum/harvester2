<?php

namespace Imamuseum\Harvester2\Jobs;

use DB;
use Exception;
use Imamuseum\Harvester2\Jobs\InitializeJob;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InitializeTexts extends InitializeJob implements ShouldQueue
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
        logger("Intializing texts from $source...\n");
    }

    /**
     * Process each text
     */
    protected function processItem($object, $model)
    {
        // remove old texts
        $model->texts()->delete();

        foreach (json_decode($object->texts) as $text) {
            $text->value = trim($text->value);
            if (empty($text->value)) {
                continue;
            }

            $text_type_id = DB::table('text_types')
                ->where('text_type_name', $text->type)
                ->value('id');

            // Insure the text type was inserted from the harvest config
            if (empty($text_type_id)) {
                throw new Exception('Text type "' . $text->type . '" doesn\'t exist');
            }

            $id = DB::table('texts')->insertGetId([
                'object_id' => $model->id,
                'text_type_id' => $text_type_id,
                'text' => $text->value,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]);
        }
    }
}
