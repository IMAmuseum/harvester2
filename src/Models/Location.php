<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;

    public function objects()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object');
    }

    public function actors()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Actor');
    }

    public function type()
    {
        return $this->belongsTo('Imamuseum\Harvester2\Models\Types\LocationType', 'location_type_id');
    }
}
