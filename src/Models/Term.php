<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    public $timestamps = false;

    public function objects()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object');
    }

    public function type()
    {
        return $this->belongsTo('Imamuseum\Harvester2\Models\Types\TermType', 'term_type_id');
    }
}
