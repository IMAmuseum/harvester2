<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    public function object()
    {
        return $this->belongsTo('Imamuseum\Harvester2\Models\Object');
    }

    public function type()
    {
        return $this->belongsTo('Imamuseum\Harvester2\Models\Types\TextType', 'text_type_id');
    }
}
