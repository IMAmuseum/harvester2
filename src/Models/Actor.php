<?php

namespace Imamuseum\Harvester2\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $guarded = [];

    public function objects()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Object')->withPivot('sequence', 'role');
    }

    public function locations()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Location');
    }

    public function dates()
    {
        return $this->belongsToMany('Imamuseum\Harvester2\Models\Date');
    }
}
